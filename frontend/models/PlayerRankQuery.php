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
      $this->joinWith('player');
      if(\Yii::$app->sys->academic_grouping!==false)
        return $this->andWhere(['player.status'=>\app\models\Player::STATUS_ACTIVE,'player.academic'=>$academic]);

      return $this->andWhere(['player.status'=>\app\models\Player::STATUS_ACTIVE]);
    }
    public function nonZero()
    {
      return $this->joinWith('score')->andWhere(['>','player_score.points',0]);
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
