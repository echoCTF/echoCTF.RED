<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "openvpn".
 *
 * @property int $id
 * @property string|null $provider_id
 * @property string|null $server
 * @property string|null $name
 * @property int|null $net
 * @property int|null $mask
 * @property int|null $management_ip
 * @property int|null $management_port
 * @property string|null $management_passwd
 * @property string|null $conf
 * @property string $created_at
 * @property string $updated_at
 */
class Openvpn extends \yii\db\ActiveRecord
{
    public $net_octet;
    public $mask_octet;
    public $management_ip_octet;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'openvpn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['net', 'mask', 'management_ip', 'management_port'], 'integer'],
            [['conf'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['provider_id', 'name', 'management_passwd','status_log','server'], 'string', 'max' => 255],
            [['net_octet','mask_octet','management_ip_octet'], 'ip'],
            [['server','name','net'], 'unique','targetAttribute'=>['server','name','net']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'server' => Yii::t('app', 'Server'),
            'name' => Yii::t('app', 'Name'),
            'net' => Yii::t('app', 'Net'),
            'mask' => Yii::t('app', 'Mask'),
            'net_octet' => Yii::t('app', 'Net'),
            'mask_octet' => Yii::t('app', 'Mask'),
            'management_ip' => Yii::t('app', 'Mgmt. Ip'),
            'management_ip_octet' => Yii::t('app', 'Mgmt. Ip'),
            'management_port' => Yii::t('app', 'Mgmt. Port'),
            'management_passwd' => Yii::t('app', 'Mgmt. Passwd'),
            'status_log'=> Yii::t('app', 'Status log'),
            'conf' => Yii::t('app', 'Conf'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return OpenvpnQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OpenvpnQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if(!parent::beforeSave($insert))
        {
            return false;
        }
        if(empty($this->net))
          $this->net=ip2long($this->net_octet);
        if(empty($this->mask))
          $this->mask=ip2long($this->mask_octet);
        if(empty($this->management_ip))
          $this->management_ip=ip2long($this->management_ip_octet);
        return true;
    }

}
