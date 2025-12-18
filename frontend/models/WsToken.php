<?php

namespace app\models;

use Yii;
use app\models\Player;

/**
 * This is the model class for table "ws_token".
 *
 * @property resource $token
 * @property int|null $player_id
 * @property resource $subject_id
 * @property int $is_server
 * @property string $expires_at
 *
 * @property Player $player
 */
class WsToken extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'ws_token';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id'], 'default', 'value' => null],

      [['token'], 'default', 'value' => function () {
        return Yii::$app->security->generateRandomString(32);
      }],

      [['is_server'], 'default', 'value' => 0],

      [['token', 'subject_id', 'expires_at'], 'required'],

      [['player_id', 'is_server'], 'integer'],
      [['subject_id'], 'string'],
      [['token'], 'string', 'max' => 32],

      [['token', 'subject_id'], 'unique'],

      [
        ['player_id'],
        'exist',
        'skipOnError' => true,
        'targetClass' => Player::class,
        'targetAttribute' => ['player_id' => 'id']
      ],
      [['expires_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
      [['expires_at'], 'default', 'value' => function () {
        return date('Y-m-d H:i:s', strtotime('+1 day'));
      }],
      [['expires_at'], 'filter', 'filter' => function ($value) {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : null;
      }],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
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
   * @return WsTokenQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new WsTokenQuery(get_called_class());
  }
}
