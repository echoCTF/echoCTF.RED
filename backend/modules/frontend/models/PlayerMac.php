<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_mac".
 *
 * @property int $id
 * @property int $player_id
 * @property string $mac
 *
 * @property Player $player
 */
class PlayerMac extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_mac';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'mac'], 'required'],
            [['player_id'], 'integer'],
            [['mac'], 'string', 'max' => 18],
            [['mac'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
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
            'mac' => 'Mac',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }
}
