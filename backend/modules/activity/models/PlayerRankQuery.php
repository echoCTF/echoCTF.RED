<?php

namespace app\modules\activity\models;

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
    }
    public function nonZero()
    {
      return $this->joinWith('score')->where(['>','player_score.points',0]);
    }

    public function active()
    {
      return $this->joinWith('player')->where(['player.active'=>1,'player.status'=>10]);
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
