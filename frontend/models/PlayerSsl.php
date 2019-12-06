<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player_ssl".
 *
 * @property int $player_id
 * @property string $subject
 * @property string $csr
 * @property string $crt
 * @property string $txtcrt
 * @property string $privkey
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerSsl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_ssl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'subject', 'csr', 'crt', 'txtcrt', 'privkey'], 'required'],
            [['player_id'], 'integer'],
            [['subject', 'csr', 'crt', 'txtcrt', 'privkey'], 'string'],
            [['ts'], 'safe'],
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
            'subject' => 'Subject',
            'csr' => 'Csr',
            'crt' => 'Crt',
            'txtcrt' => 'Txtcrt',
            'privkey' => 'Privkey',
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
     * @return PlayerSslQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerSslQuery(get_called_class());
    }
}
