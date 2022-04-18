<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "inquiry".
 *
 * @property int $id
 * @property int $player_id
 * @property int $answered
 * @property string|null $category
 * @property string|null $name
 * @property string|null $email
 * @property string|null $serialized
 * @property string|null $body
 * @property string $updated_at
 * @property string|null $created_at
 *
 * @property Player $player
 */
class Inquiry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inquiry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['player_id'], 'integer'],
            [[ 'answered'], 'boolean'],
            [['serialized', 'body'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['category', 'name', 'email'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
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
            'answered' => Yii::t('app', 'Answered'),
            'category' => Yii::t('app', 'Category'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'serialized' => Yii::t('app', 'Serialized'),
            'body' => Yii::t('app', 'Body'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * @return InquiryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InquiryQuery(get_called_class());
    }
}
