<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[TargetOndemand]].
 *
 * @see TargetOndemand
 */
class TargetOndemandQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetOndemand[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetOndemand|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
