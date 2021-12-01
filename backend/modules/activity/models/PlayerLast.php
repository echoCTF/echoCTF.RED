<?php

namespace app\modules\activity\models;

use Yii;

/**
 * This is the model class for table "player_last".
 *
 * @property int $id
 * @property string $on_pui
 * @property string $on_vpn
 * @property int $vpn_remote_address
 * @property int $vpn_local_address
 */
class PlayerLast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_last';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['on_pui', 'on_vpn'], 'safe'],
            [['vpn_remote_address', 'vpn_local_address','signup_ip','signin_ip'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Player ID',
            'on_pui' => 'On Pui',
            'on_vpn' => 'On Vpn',
            'vpn_remote_address' => 'Vpn Remote Address',
            'vpn_local_address' => 'Vpn Local Address',
            'signin_ip' => 'Signin IP',
            'signup_ip' => 'Signup IP',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(\app\modules\frontend\models\Player::class, ['id' => 'id']);
    }

}
