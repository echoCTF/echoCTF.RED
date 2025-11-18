<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[PlayerProduct]].
 *
 * @see PlayerProduct
 */
class PlayerProductQuery extends \yii\db\ActiveQuery
{
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
