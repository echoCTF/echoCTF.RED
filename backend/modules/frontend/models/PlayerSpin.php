<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_spin".
 *
 * @property int $player_id
 * @property int $counter
 * @property int $total
 * @property int $perday
 * @property string $updated_at
 *
 * @property Player $player
 */
class PlayerSpin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_spin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['player_id', 'counter', 'total','perday'], 'integer'],
            [['updated_at'], 'safe'],
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
            'player_id' => 'Player ID',
            'counter' => 'Counter',
            'total' => 'Total',
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
}
