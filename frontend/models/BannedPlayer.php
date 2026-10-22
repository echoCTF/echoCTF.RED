<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "banned_player".
 *
 * @property int $id
 * @property int $old_id
 * @property string $username
 * @property string $email
 * @property string $registered_at
 * @property string $banned_at
 */
class BannedPlayer extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'banned_player';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['old_id'], 'integer'],
      [['registered_at', 'banned_at'], 'safe'],
      [['username'], 'string', 'max' => 32],
      [['email'], 'string', 'max' => 128],
      [['email'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'old_id' => 'Old ID',
      'username' => 'Username',
      'email' => 'Email',
      'registered_at' => 'Registered At',
      'banned_at' => 'Banned At',
    ];
  }

  public static function isBanned(string $email): bool
  {
    return static::find()->where(new Expression(':email LIKE email', [':email' => $email]))->exists();
  }

  public function save($runValidation = true, $attributeNames = null)
  {
    throw new \LogicException("Saving is disabled for this model.");
  }
}
