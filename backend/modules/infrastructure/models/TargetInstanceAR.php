<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;

/**
 * This is the model class for table "target_instance".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int|null $server_id
 * @property int|null $ip
 * @property int $reboot
 * @property bool $team_allowed
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Target $target
 * @property Server $server
 */
class TargetInstanceAR extends \yii\db\ActiveRecord
{

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
          'reboot' => AttributeTypecastBehavior::TYPE_INTEGER,
          'team_allowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
        ],
        'typecastAfterValidate' => false,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
      'timestamp' => [
        'class' => TimestampBehavior::class,
        'value' => new Expression('NOW()'),
      ]
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
      [['team_allowed'], 'boolean',],
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
      'team_allowed' => Yii::t('app', 'Team Allowed'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
    ];
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
   * Gets query for [[Target]].
   *
   * @return \yii\db\ActiveQuery|TargetQuery
   */
  public function getServer()
  {
    return $this->hasOne(Server::class, ['id' => 'server_id']);
  }

  /**
   * {@inheritdoc}
   * @return TargetInstanceQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new TargetInstanceQuery(get_called_class());
  }
}
