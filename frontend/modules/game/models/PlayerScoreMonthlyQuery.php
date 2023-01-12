<?php

namespace app\modules\game\models;

/**
 * This is the ActiveQuery class for [[PlayerScore]].
 *
 * @see PlayerScore
 */
class PlayerScoreMonthlyQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
      return $this->joinWith('player')->where(['player.status'=>\app\models\Player::STATUS_ACTIVE]);
    }

    public function currentMonth()
    {
      return $this->andWhere(['=','dated_at',date('Ym')]);
    }

    public function nonZero()
    {
      return $this->andWhere(['>','points',0]);
    }

    public function ordered()
    {
      $this->orderBy(['points' => SORT_DESC, 'ts' => SORT_ASC,'player_id'=>SORT_ASC]);
      return $this;
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
