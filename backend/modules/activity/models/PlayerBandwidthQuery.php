<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerBandwidth]].
 *
 * @see PlayerBandwidth
 */
class PlayerBandwidthQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerBandwidth[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerBandwidth|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
