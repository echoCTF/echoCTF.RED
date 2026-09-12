<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "ws_token_history".
 *
 * @property int $id
 * @property resource $token
 * @property int|null $player_id
 * @property resource $subject_id
 * @property int $is_server
 * @property string $expires_at
 */
class WsTokenHistory extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'ws_token_history';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id'], 'default', 'value' => null],
      [['is_server'], 'default', 'value' => 0],
      [['token', 'subject_id', 'expires_at'], 'required'],
      [['player_id', 'is_server'], 'integer'],
      [['expires_at'], 'safe'],
      [['token', 'subject_id'], 'string', 'max' => 32],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'token' => Yii::t('app', 'Token'),
      'player_id' => Yii::t('app', 'Player ID'),
      'subject_id' => Yii::t('app', 'Subject ID'),
      'is_server' => Yii::t('app', 'Is Server'),
      'expires_at' => Yii::t('app', 'Expires At'),
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
   * @return WsTokenHistoryQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new WsTokenHistoryQuery(get_called_class());
  }
}
