<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int $player_id
 * @property string $category
 * @property string $title
 * @property string $body
 * @property int $archived
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Player $player
 */
class Notification extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'notification';
  }

  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
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
      //            [['player_id'], 'required'],
      [['player_id'], 'integer'],
      [['archived'], 'boolean'],
      [['body'], 'string'],
      [['created_at', 'updated_at'], 'safe'],
      [['title'], 'string', 'max' => 255],
      [['category'], 'string', 'max' => 20],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'player_id' => 'Player ID',
      'title' => 'Title',
      'body' => 'Body',
      'archived' => 'Archived',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }
  public static function supportedCategories(): array
  {
    return [
      'info'=>'info',
      'danger'=>'danger',
      'success'=>'success',
      'warning'=>'warning',
      'rose'=>'rose',
      'primary'=>'primary',
      'swal:info'=>'swal:info',
      'swal:error'=>'swal:error',
      'swal:danger'=>'swal:danger',
      'swal:success'=>'swal:success',
      'swal:warning'=>'swal:warning',
    ];
  }
}
