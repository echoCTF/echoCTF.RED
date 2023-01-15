<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[Price]].
 *
 * @see Price
 */
class PriceQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    /**
     * {@inheritdoc}
     * @return Price[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Price|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
