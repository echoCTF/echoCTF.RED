<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[SpinQueue]].
 *
 * @see SpinQueue
 */
class SpinQueueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SpinQueue[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SpinQueue|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
