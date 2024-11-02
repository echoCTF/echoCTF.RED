<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_disconnect_queue".
 *
 * @property int $player_id
 * @property string $created_at
 *
 * @property Player $player
 */
class PlayerDisconnectQueue extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_disconnect_queue';
  }
  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => null,
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
      [['player_id'], 'required'],
      [['player_id'], 'integer'],
      [['created_at'], 'safe'],
      [['player_id'], 'unique'],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'player_id' => Yii::t('app', 'Player ID'),
      'created_at' => Yii::t('app', 'Created At'),
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
   * {@inheritdoc}
   * @return PlayerDisconnectQueueQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerDisconnectQueueQuery(get_called_class());
  }
}
