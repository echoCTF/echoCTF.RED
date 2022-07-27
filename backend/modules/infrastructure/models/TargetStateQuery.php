<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetState]].
 *
 * @see TargetState
 */
class TargetStateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetState[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetState|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
