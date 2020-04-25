<?php

namespace app\modules\activity\models;

use Yii;

/**
 * This is the model class for table "player_vpn_history".
 *
 * @property int $id
 * @property int $player_id
 * @property int $vpn_remote_address
 * @property int $vpn_local_address
 * @property string $ts
 */
class PlayerVpnHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_vpn_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'vpn_remote_address', 'vpn_local_address'], 'integer'],
            [['ts'], 'safe'],
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
            'vpn_remote_address' => 'Vpn Remote Address',
            'vpn_local_address' => 'Vpn Local Address',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(\app\modules\frontend\models\Player::class, ['id' => 'player_id']);
    }
}
