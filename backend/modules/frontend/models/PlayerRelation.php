<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_relation".
 *
 * @property int $player_id
 * @property int|null $referred_id
 *
 * @property Player $player
 */
class PlayerRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['player_id', 'referred_id'], 'integer'],
            [['player_id'], 'unique'],
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
            'referred_id' => Yii::t('app', 'Referred ID'),
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
     * Gets query for referred [[Player]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getReferred()
    {
        return $this->hasOne(Player::class, ['id' => 'referred_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerRelationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerRelationQuery(get_called_class());
    }
}
