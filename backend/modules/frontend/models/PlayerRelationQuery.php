<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[PlayerRelation]].
 *
 * @see PlayerRelation
 */
class PlayerRelationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerRelation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerRelation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
