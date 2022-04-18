<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "writeup_rating".
 *
 * @property int $id
 * @property int $writeup_id
 * @property int $player_id
 * @property int $rating
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Writeup $writeup
 */
class WriteupRating extends \yii\db\ActiveRecord
{
  public function behaviors()
  {
    return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'rating' => AttributeTypecastBehavior::TYPE_INTEGER,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
            'typecastAfterFind' => true,
        ],
        'timestamp'=>[
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
            [['writeup_id', 'player_id'], 'required'],
            [['writeup_id', 'player_id', 'rating'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['writeup_id', 'player_id'], 'unique', 'targetAttribute' => ['writeup_id', 'player_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['writeup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Writeup::class, 'targetAttribute' => ['writeup_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'writeup_id' => Yii::t('app', 'Writeup ID'),
            'player_id' => Yii::t('app', 'Player ID'),
            'rating' => Yii::t('app', 'Rating'),
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
     * Gets query for [[Writeup]].
     *
     * @return \yii\db\ActiveQuery|WriteupQuery
     */
    public function getWriteup()
    {
        return $this->hasOne(Writeup::class, ['id' => 'writeup_id']);
    }

    /**
     * {@inheritdoc}
     * @return WriteupRatingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WriteupRatingQuery(get_called_class());
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'writeup_rating';
    }

}
