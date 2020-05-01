<?php

namespace app\modules\network\models;

/**
 * This is the ActiveQuery class for [[NetworkTarget]].
 *
 * @see NetworkTarget
 */
class NetworkTargetQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NetworkTarget[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NetworkTarget|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
