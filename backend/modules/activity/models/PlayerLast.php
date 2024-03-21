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
  public $signin_ipoctet;
  public $signup_ipoctet;
  public $vpn_remote_address_octet;
  public $vpn_local_address_octet;

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
            [['signup_ipoctet','signin_ipoctet','vpn_local_address_octet','vpn_remote_address_octet'], 'ip'],

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
    public function resetVPN() {
      return $this->updateAttributes(['vpn_remote_address' => null,'vpn_local_address'=>null]);
    }

    public function afterFind() {
      parent::afterFind();
      $this->signin_ipoctet=long2ip($this->signin_ip);
      $this->signup_ipoctet=long2ip($this->signup_ip);
      $this->vpn_remote_address_octet=long2ip($this->vpn_remote_address);
      $this->vpn_local_address_octet=long2ip($this->vpn_local_address);
    }

    public function beforeSave($insert)
    {
      if(parent::beforeSave($insert))
      {
        $this->signin_ip=ip2long($this->signin_ipoctet);
        $this->signup_ip=ip2long($this->signup_ipoctet);
        $this->vpn_remote_address=ip2long($this->vpn_remote_address_octet);
        $this->vpn_local_address=ip2long($this->vpn_local_address_octet);
        return true;
      }
      else
      {
          return false;
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(\app\modules\frontend\models\Player::class, ['id' => 'id']);
    }

}
