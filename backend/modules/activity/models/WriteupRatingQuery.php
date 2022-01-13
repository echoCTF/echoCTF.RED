<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[WriteupRating]].
 *
 * @see WriteupRating
 */
class WriteupRatingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return WriteupRating[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WriteupRating|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
