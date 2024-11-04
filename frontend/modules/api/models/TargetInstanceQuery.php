<?php

namespace app\modules\api\models;

/**
 * This is the ActiveQuery class for [[TargetInstance]].
 *
 * @see TargetInstance
 */
class TargetInstanceQuery extends \yii\db\ActiveQuery
{
  public function rest()
  {
    return $this->select(['target_id', 'target_instance.ip', new \yii\db\Expression('INET_NTOA(target_instance.ip) as ipstr'), 't.name as hostname', 't.fqdn', 'p.username as owner'])->joinWith(['target t', 'player p']);
  }

  /**
   * {@inheritdoc}
   * @return Headshot[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return Headshot|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
