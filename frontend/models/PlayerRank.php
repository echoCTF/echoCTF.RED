<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player_rank".
 *
 * @property int $id
 * @property int $player_id
 */
class PlayerRank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_rank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'required'],
            [['id', 'player_id'], 'integer'],
            [['player_id'], 'unique'],
            [['id'], 'unique'],
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
        ];
    }
}
