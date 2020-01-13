<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[Headshot]].
 *
 * @see Headshot
 */
class HeadshotQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Headshot[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Headshot|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
