<?php

namespace app\modules\target\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "spin_queue".
 *
 * @property int $target_id
 * @property int $player_id
 * @property string|null $created_at
 *
 * @property Player $player
 * @property Target $target
 */
class SpinQueue extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'spin_queue';
  }

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
          'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
      [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'created_at',
        'value' => new Expression('NOW()'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['target_id', 'player_id'], 'required'],
      [['target_id', 'player_id'], 'integer'],
      [['created_at'], 'safe'],
      [['target_id'], 'unique'],
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
      'target_id' => 'Target ID',
      'player_id' => 'Player ID',
      'created_at' => 'Created At',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'target_id']);
  }

  /**
   * {@inheritdoc}
   * @return SpinQueueQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new SpinQueueQuery(get_called_class());
  }

  public static function queue(Player $player, int $targetId): Target
  {
    $target = Target::find()->player_progress($player->id)->where(['t.id' => $targetId])->one();
    if ($target === null) {
      throw new NotFoundHttpException(Yii::t('app', 'The requested target does not exist.'));
    }

    Yii::$app->getModule('target')->checkNetwork($target);

    if (Yii::$app->sys->disable_ondemand_operations || $target->spinable !== true) {
      throw new NotFoundHttpException(Yii::t('app', 'Not allowed to spin target. Target cannot be spun.'));
    }

    $playerSpin = $player->profile->spins;
    $SQ = new self;
    $SQ->player_id = (int) $player->id;
    $SQ->target_id = $target->id;
    $playerSpin->counter = intval($playerSpin->counter) + 1;
    $playerSpin->total = intval($playerSpin->total) + 1;
    if ($SQ->save() === false || $playerSpin->save() === false) {
      throw new NotFoundHttpException(Yii::t('app', 'Failed to queue target for reboot.'));
    }

    if (Yii::$app->sys->spins_per_day !== false && intval($playerSpin->counter) >= intval(Yii::$app->sys->spins_per_day)) {
      $player->notify("swal:info", Yii::t('app', "Max spins reached"), Yii::t('app', 'You have reached the maximum number of spins for the day. Feel free to reach out to our support to reset your counter!'));
    }
    return $target;
  }
}
