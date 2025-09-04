<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerScoreMonthly]].
 *
 * @see PlayerScoreMonthly
 */
class PlayerScoreMonthlyQuery extends \yii\db\ActiveQuery
{
  public function init()
  {
    parent::init();

    if ($this->select === null) {
      $this->select(['player_score_monthly.*', 'TS_AGO(player_score_monthly.ts) as ts_ago']);
    }
  }


  /**
   * {@inheritdoc}
   * @return PlayerScoreMonthly[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return PlayerScoreMonthly|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
