<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerScore]].
 *
 * @see PlayerScore
 */
class PlayerRankQuery extends \yii\db\ActiveQuery
{
    public function academic($academic)
    {
      return $this->joinWith('player')->where(['player.academic'=>$academic]);
        //return $this->andWhere('[[status]]=1');
    }

    /**
     * {@inheritdoc}
     * @return PlayerScore[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerScore|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
