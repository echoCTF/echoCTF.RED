<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[ChallengeSolver]].
 *
 * @see ChallengeSolver
 */
class ChallengeSolverQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChallengeSolver[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChallengeSolver|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
