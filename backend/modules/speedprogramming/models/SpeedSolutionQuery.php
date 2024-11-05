<?php

namespace app\modules\speedprogramming\models;

/**
 * This is the ActiveQuery class for [[SpeedSolution]].
 *
 * @see SpeedSolution
 */
class SpeedSolutionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SpeedSolution[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SpeedSolution|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
