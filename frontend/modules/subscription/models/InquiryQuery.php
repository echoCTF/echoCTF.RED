<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[Inquiry]].
 *
 * @see Inquiry
 */
class InquiryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Inquiry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Inquiry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
