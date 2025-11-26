<?php

namespace app\modules\network\models;

/**
 * This is the ActiveQuery class for [[PrivateNetwork]].
 *
 * @see PrivateNetwork
 */
class PrivateNetworkQuery extends \yii\db\ActiveQuery
{
  public function forTeam($id)
  {
    return $this->andWhere('player_id IN (SELECT player_id FROM team_player WHERE team_id=:id and approved=1)',[':id'=>$id]);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetwork[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetwork|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
