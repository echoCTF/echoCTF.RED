<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[SpinHistory]].
 *
 * @see SpinHistory
 */
class SpinHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');`
    }*/

    /**
     * {@inheritdoc}
     * @return SpinHistory[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SpinHistory|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
