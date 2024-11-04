<?php

namespace app\models;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;

use Yii;

/**
 * This is the model class for table "player_token".
 *
 * @property int $player_id
 * @property string $type
 * @property string $token
 * @property string $expires_at
 * @property string $created_at
 *
 * @property Player $player
 */
class PlayerToken extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_token';
  }

  public function init(){
    parent::init();
    if(!method_exists($this,'search') && $this->isNewRecord) //for checking this code is on model search or not
    {
      $this->type='API';
      $this->token=Yii::$app->security->generateRandomString(30);
      $this->expires_at=\Yii::$app->formatter->asDatetime(new \DateTime('NOW + 60 days'), 'php:Y-m-d H:i:s');
    }
  }
  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
      'timestamp' => [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'created_at',
        'value' => new Expression('NOW()'),
        'preserveNonEmptyValues' => true,
      ],
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      ['token', 'default', 'value' => Yii::$app->security->generateRandomString(30)],
      [['expires_at'], 'default', 'value' => \Yii::$app->formatter->asDatetime(new \DateTime('NOW + 30 days'), 'php:Y-m-d H:i:s')],
      [['type'], 'default', 'value' => 'API'],
      [['player_id', 'type', 'token'], 'required'],
      [['player_id'], 'integer'],
      [['expires_at', 'created_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
      [['expires_at', 'created_at','description'], 'safe'],
      [['type'], 'string', 'max' => 32],
      [['token'], 'string', 'max' => 128],
      [['token'], 'unique'],
      [['player_id', 'type'], 'unique', 'targetAttribute' => ['player_id', 'type']],
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
      'type' => Yii::t('app', 'Type'),
      'token' => Yii::t('app', 'Token'),
      'expires_at' => Yii::t('app', 'Expires At'),
      'created_at' => Yii::t('app', 'Created At'),
    ];
  }

  /**
   * Gets query for [[Player]].
   *
   * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * {@inheritdoc}
   * @return PlayerTokenQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerTokenQuery(get_called_class());
  }

  public function getTypes()
  {
    return [
      'API'=>'API',
      'password_reset'=>'Password Reset',
      'email_verification'=>'Email Verification'
    ];
  }
}
