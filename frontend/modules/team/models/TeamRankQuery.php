<?php

namespace app\modules\team\models;

/**
 * This is the ActiveQuery class for [[TeamRank]].
 *
 * @see TeamRank
 */
class TeamRankQuery extends \yii\db\ActiveQuery
{
    public function academic($academic)
    {
      return $this->joinWith('team')->where(['team.academic'=>$academic]);
        //return $this->andWhere('[[status]]=1');
    }

    /**
     * {@inheritdoc}
     * @return TeamRank[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeamRank|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
