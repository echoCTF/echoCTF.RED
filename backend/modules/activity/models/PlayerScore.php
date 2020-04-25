<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_score".
 *
 * @property int $player_id
 * @property int $points
 */
class PlayerScore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_score';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['player_id', 'points'], 'integer'],
            [['player_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'points' => 'Points',
        ];
    }
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

}
