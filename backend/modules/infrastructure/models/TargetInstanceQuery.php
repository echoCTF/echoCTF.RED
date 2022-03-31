<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetInstance]].
 *
 * @see TargetInstance
 */
class TargetInstanceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetInstance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetInstance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
