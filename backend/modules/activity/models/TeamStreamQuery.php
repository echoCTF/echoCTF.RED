<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[TeamStream]].
 *
 * @see TeamStream
 */
class TeamStreamQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TeamStream[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeamStream|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
