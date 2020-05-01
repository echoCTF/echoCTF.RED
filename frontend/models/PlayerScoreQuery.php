<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerScore]].
 *
 * @see PlayerScore
 */
class PlayerScoreQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
      return $this->joinWith('player')->where(['player.status'=>Player::STATUS_ACTIVE]);
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
