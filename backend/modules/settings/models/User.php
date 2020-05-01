<?php

namespace app\modules\settings\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $verification_token
 * @property int $admin
 */
class User extends \yii\db\ActiveRecord
{
    public $new_password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['status', 'created_at', 'updated_at', 'admin'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['auth_key'], 'default', 'value' => Yii::$app->security->generateRandomString()],
            [['password_reset_token'], 'default', 'value' => Yii::$app->security->generateRandomString().'_'.time()],
            [['verification_token'], 'default', 'value' => Yii::$app->security->generateRandomString().'_'.time()],
            [['status'], 'default', 'value'=>10],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token', 'auth_key'], 'unique'],
            [['new_password', 'password_hash', 'auth_key'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'admin' => 'Admin',
            'new_password'=>'New Password',
        ];
    }
    public function beforeSave($insert)
    {
      if($this->new_password != "")
      {
          $this->password_hash=Yii::$app->security->generatePasswordHash($this->new_password);
      }
      return parent::beforeSave($insert);
    }
}
