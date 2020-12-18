<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;
/**
 * This is the model class for table "stream".
 *
 * @property string $id
 * @property int $player_id
 * @property string $model
 * @property int $model_id
 * @property int $points
 * @property string $title
 * @property string $message
 * @property string $pubtitle
 * @property string $pubmessage
 * @property string $ts
 *
 * @property Player $player
 */
class StreamAR extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
      return 'stream';
  }

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
          'points' => AttributeTypecastBehavior::TYPE_FLOAT,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => false,
        'typecastAfterFind' => true,
      ],
      [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'ts',
        'updatedAtAttribute' => 'ts',
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
        [['player_id', 'model_id', 'points'], 'integer'],
        [['player_id', 'title', 'message', 'pubtitle', 'pubmessage'], 'required'],
        [['message', 'pubmessage'], 'string'],
        [['points'], 'default', 'value' => 0],
        [['ts'], 'safe'],
        [['model', 'title', 'pubtitle'], 'string', 'max' => 255],
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
      'model' => 'Model',
      'model_id' => 'Model ID',
      'points' => 'Points',
      'title' => 'Title',
      'message' => 'Message',
      'pubtitle' => 'Pubtitle',
      'pubmessage' => 'Pubmessage',
      'ts' => 'Ts',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }
}
