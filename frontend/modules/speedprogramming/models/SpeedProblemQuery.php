<?php

namespace app\modules\speedprogramming\models;

/**
 * This is the ActiveQuery class for [[SpeedProblem]].
 *
 * @see SpeedProblem
 */
class SpeedProblemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SpeedProblem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SpeedProblem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
