<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[Writeup]].
 *
 * @see Writeup
 */
class WriteupQuery extends \yii\db\ActiveQuery
{
    //public function active()
    //{
    //    return $this->andWhere('[[status]]=1');
    //}
    public function byStatus($status)
    {
        return $this->andWhere(['status'=>$status]);
    }

    /**
     * {@inheritdoc}
     * @return Writeup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Writeup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
