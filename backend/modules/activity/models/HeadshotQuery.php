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
     * Return all records
     *
     * @return Headshot[]|array|null
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * Return one record
     *
     * @return Headshot|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
