<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[Product]].
 *
 * @see Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    public function hasPrice()
    {
        return $this->andWhere('id in (SELECT DISTINCT product_id FROM price)');
    }

    public function purchasable()
    {
        return $this->active()->hasPrice();
    }

    public function ordered()
    {
        return $this->orderBy(['weight'=>SORT_ASC,'name'=>SORT_ASC]);
    }
    /**
     * {@inheritdoc}
     * @return Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
