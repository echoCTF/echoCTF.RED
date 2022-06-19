<?php

namespace app\modules\content\models;

/**
 * This is the ActiveQuery class for [[LayoutOverride]].
 *
 * @see LayoutOverride
 */
class LayoutOverrideQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LayoutOverride[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LayoutOverride|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
