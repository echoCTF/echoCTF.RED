<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_last".
 *
 * @property int $id
 * @property string $ts
 * @property string $on_pui
 * @property string $on_vpn
 * @property int|null $vpn_remote_address
 * @property int|null $vpn_local_address
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
            [['ts', 'on_pui', 'on_vpn'], 'safe'],
            [['vpn_remote_address', 'vpn_local_address'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ts' => 'Ts',
            'on_pui' => 'On Pui',
            'on_vpn' => 'On Vpn',
            'vpn_remote_address' => 'Vpn Remote Address',
            'vpn_local_address' => 'Vpn Local Address',
        ];
    }
}
