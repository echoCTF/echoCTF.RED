<?php

namespace app\modules\help\models;

/**
 * This is the ActiveQuery class for [[Rule]].
 *
 * @see Rule
 */
class RuleQuery extends \yii\db\ActiveQuery
{
  public function forPlayerType($player_type = "offense")
  {
    return $this->andWhere(['OR',['player_type'=>$player_type],['player_type'=>'both']]);
  }

  /**
   * {@inheritdoc}
   * @return Rule[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return Rule|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
