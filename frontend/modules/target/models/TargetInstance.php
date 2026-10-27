<?php

namespace app\modules\target\models;

use Yii;
use app\models\Player;
use app\modules\target\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "target_instance".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int|null $server_id
 * @property int|null $ip
 * @property int $reboot
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Target $target
 */
class TargetInstance extends \yii\db\ActiveRecord
{
  public $ipoctet;
  const ACTION_START = 0;
  const ACTION_RESTART = 1;
  const ACTION_DESTROY = 2;
  const ACTION_EXPIRED = 3;


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'target_instance';
  }

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'ip' => AttributeTypecastBehavior::TYPE_INTEGER,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => false,
        'typecastAfterFind' => true,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id', 'target_id'], 'required'],
      [['player_id', 'target_id', 'server_id', 'ip', 'reboot'], 'integer'],
      [['ipoctet'], 'ip'],
      ['reboot', 'default', 'value' => 0],
      ['reboot', 'in', 'range' => [0, 1, 2]],
      [['created_at', 'updated_at'], 'safe'],
      [['player_id'], 'unique'],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
      [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'player_id' => Yii::t('app', 'Player ID'),
      'target_id' => Yii::t('app', 'Target ID'),
      'server_id' => Yii::t('app', 'Server ID'),
      'ip' => Yii::t('app', 'IP'),
      'reboot' => Yii::t('app', 'Reboot'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
    ];
  }

  /**
   * Gets name tha the target will have
   */
  public function getName()
  {
    return sprintf("%s_%d", strtolower($this->target->name), $this->player_id);
  }
  /**
   * Gets query for [[Player]].
   *
   * @return \yii\db\ActiveQuery|PlayerQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * Gets query for [[Target]].
   *
   * @return \yii\db\ActiveQuery|TargetQuery
   */
  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'target_id']);
  }

  /**
   * {@inheritdoc}
   * @return TargetInstanceQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new TargetInstanceQuery(get_called_class());
  }

  public static function spawnFor(Player $player, int $targetId, bool $team = false): array
  {
    $target = Target::findOne($targetId);
    if ($target === null) {
      throw new NotFoundHttpException(Yii::t('app', 'The requested target does not exist.'));
    }
    $playerSpin = $player->profile->spins;

    if (Yii::$app->DisabledRoute->disabled(sprintf('/target/%d/spawn', $target->id))) {
      throw new HttpException(403, Yii::t('app', 'This route is disabled!'));
    }
    if (!$target->instance_allowed) {
      throw new HttpException(403, Yii::t('app', 'Target not allowed to spawn private instances!'));
    }
    if (!\app\modules\target\Module::canSpawnInstances($player)) {
      throw new HttpException(403, Yii::t('app', 'You are not allowed to spawn private instances!'));
    }
    if (intval($playerSpin->counter) >= intval($playerSpin->perday)) {
      throw new HttpException(403, Yii::t('app', "You've used all your spins; your counter is maxed out!"));
    }
    if ($target->status !== 'online') {
      throw new HttpException(422, Yii::t('app', 'Target did not start, target is not online yet!'));
    }

    $ti = self::findOne($player->id);
    if ($ti !== null) {
      if ($ti->target_id != $targetId) {
        $ti->reboot = 2;
        $ti->save();
        return ['old_shutdown', $ti];
      }
      $playerSpin->counter = intval($playerSpin->counter) + 1;
      $playerSpin->total = intval($playerSpin->total) + 1;
      $playerSpin->save();
      $ti->reboot = 1;
      $ti->save();
      return ['restarted', $ti];
    }

    $ti = new self;
    $ti->player_id = $player->id;
    $ti->target_id = $targetId;
    $ti->team_allowed = intval(Yii::$app->sys->team_visible_instances);
    if ($player->subscription !== null && $player->subscription->active > 0 && $player->subscription->product !== null) {
      $metadata = json_decode($player->subscription->product->metadata);
      if (isset($metadata->team_subscription) && boolval($metadata->team_subscription) === true) {
        $ti->team_allowed = $team ? 1 : 0;
      }
    }
    // pick the least used server currently, ties are random
    $ti->server_id = intval(Yii::$app->db->createCommand('select id from server t1 left join target_instance t2 on t1.id=t2.server_id group by t1.id order by count(t2.server_id), rand() limit 1')->queryScalar());
    if ($ti->save() === false) {
      throw new HttpException(422, Yii::t('app', 'Failed to spawn new target instance for you.'));
    }
    $playerSpin->counter = intval($playerSpin->counter) + 1;
    $playerSpin->total = intval($playerSpin->total) + 1;
    $playerSpin->save();
    return ['spawned', $ti];
  }

  public function afterFind()
  {
    parent::afterFind();
    if ($this->ip)
      $this->ipoctet = long2ip($this->ip);
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if ($this->ipoctet)
        $this->ip = ip2long($this->ipoctet);
      else $this->ip = null;
      return true;
    } else {
      return false;
    }
  }

  public function getRebootVal()
  {
    if ($this->reboot === 0 && $this->ip === null) {
      return "Start";
    } elseif ($this->reboot === 0) {
      return "Do nothing";
    } elseif ($this->reboot === 1) {
      return "Restart";
    } elseif ($this->reboot === 2) {
      return "Destroy";
    }
  }
}
