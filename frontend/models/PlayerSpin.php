<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player_spin".
 *
 * @property int $player_id
 * @property int|null $counter
 * @property int|null $total
 * @property string|null $updated_at
 * @property string $ts
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
            [['player_id', 'counter', 'total'], 'integer'],
            [['updated_at', 'ts'], 'safe'],
            [['player_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
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
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSpinQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerSpinQuery(get_called_class());
    }
}
