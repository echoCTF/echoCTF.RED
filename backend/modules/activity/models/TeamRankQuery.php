<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[TeamScore]].
 *
 * @see TeamScore
 */
class TeamRankQuery extends \yii\db\ActiveQuery
{
    public function academic($academic)
    {
      return $this->joinWith('team')->where(['team.academic'=>$academic]);
    }
    public function nonZero()
    {
      return $this->joinWith('score')->where(['>','team_score.points',0]);
    }

    /**
     * {@inheritdoc}
     * @return TeamScore[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeamScore|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
