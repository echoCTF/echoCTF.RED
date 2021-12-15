<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[ProductNetwork]].
 *
 * @see ProductNetwork
 */
class ProductNetworkQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductNetwork[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductNetwork|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
