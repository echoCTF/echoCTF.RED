<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player_score".
 *
 * @property int $player_id
 * @property int $points
 * @property string $ts
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
            [['ts'], 'safe'],
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
            'ts' => 'Ts',
        ];
    }
    public function getPlayer()
    {
      return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }
    public function getRank()
    {
      return $this->hasOne(PlayerRank::className(), ['player_id' => 'player_id']);
    }



}
