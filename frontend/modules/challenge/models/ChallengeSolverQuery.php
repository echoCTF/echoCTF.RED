<?php

namespace app\modules\challenge\models;

/**
 * This is the ActiveQuery class for [[ChallengeSolver]].
 *
 * @see ChallengeSolver
 */
class ChallengeSolverQuery extends \yii\db\ActiveQuery
{
    public function academic($academic)
    {
      return $this->joinWith('player')->andWhere(['player.academic'=>$academic]);
    }

    public function timed()
    {
      return $this->joinWith(['challenge'])->andWhere(['challenge.timer'=>1])->andWhere(['>','challenge_solver.timer',0]);
    }

    /**
     * {@inheritdoc}
     * @return ChallengeSolver[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChallengeSolver|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
