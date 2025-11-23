<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[PlayerProduct]].
 *
 * @see PlayerProduct
 */
class PlayerProductQuery extends \yii\db\ActiveQuery
{
  public function mine()
  {
    return $this->andWhere(['player_id' => \Yii::$app->user->id]);
  }

  public function active()
  {
    return $this->andWhere(['>', 'ending', new \yii\db\Expression('NOW() - INTERVAL 1 DAY')]);
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
