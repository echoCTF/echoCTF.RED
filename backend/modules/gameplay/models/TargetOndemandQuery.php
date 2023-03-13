<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[TargetOndemand]].
 *
 * @see TargetOndemand
 */
class TargetOndemandQuery extends \yii\db\ActiveQuery
{
  public function withExpired()
  {
    return $this->select(['*', '(60*60)-TIMESTAMPDIFF(SECOND, heartbeat,now()) AS expired']);
  }

  public function powered()
  {
    return $this->andWhere('[[state]]=1');
  }

  /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

  /**
   * {@inheritdoc}
   * @return TargetOndemand[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return TargetOndemand|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
