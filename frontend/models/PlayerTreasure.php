<?php

namespace app\models;

use Yii;
use app\modules\target\models\Treasure;

/**
 * This is the model class for table "player_treasure".
 *
 * @property int $player_id
 * @property int $treasure_id
 * @property string $ts
 *
 * @property Player $player
 * @property Treasure $treasure
 */
class PlayerTreasure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_treasure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'treasure_id'], 'required'],
            [['player_id', 'treasure_id'], 'integer'],
            [['ts'], 'safe'],
            [['player_id', 'treasure_id'], 'unique', 'targetAttribute' => ['player_id', 'treasure_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
            [['treasure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treasure::className(), 'targetAttribute' => ['treasure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'treasure_id' => 'Treasure ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTreasure()
    {
        return $this->hasOne(Treasure::className(), ['id' => 'treasure_id']);
    }
}
