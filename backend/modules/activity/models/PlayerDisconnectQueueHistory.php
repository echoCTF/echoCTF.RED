<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_disconnect_queue_history".
 *
 * @property int $id
 * @property int $player_id
 * @property string $created_at
 */
class PlayerDisconnectQueueHistory extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_disconnect_queue_history';
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
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
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
   * @return PlayerDisconnectQueueHistoryQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerDisconnectQueueHistoryQuery(get_called_class());
  }
}
