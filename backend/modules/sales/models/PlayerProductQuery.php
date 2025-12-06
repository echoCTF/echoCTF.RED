<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[PlayerProduct]].
 *
 * @see PlayerProduct
 */
class PlayerProductQuery extends \yii\db\ActiveQuery
{
  public function expired($interval = null)
  {
    if ($interval === null)
      return $this->andWhere('ending < NOW()');

    return $this->andWhere(['<', 'ending', new \yii\db\Expression("NOW() - INTERVAL $interval")]);
  }
  /**
   * {@inheritdoc}
   * @return PlayerProduct[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return PlayerProduct|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
