<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[PlayerPaymentHistory]].
 *
 * @see PlayerPaymentHistory
 */
class PlayerPaymentHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerPaymentHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerPaymentHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
