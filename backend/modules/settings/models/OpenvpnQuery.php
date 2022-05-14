<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[Openvpn]].
 *
 * @see Openvpn
 */
class OpenvpnQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        // the following allows us to set a default select query
        $this->addSelect(["*","INET_NTOA(mask) AS mask_octet","INET_NTOA(net) AS net_octet","INET_NTOA(management_ip) AS management_ip_octet"]);
        parent::init();
    }
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Openvpn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Openvpn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
