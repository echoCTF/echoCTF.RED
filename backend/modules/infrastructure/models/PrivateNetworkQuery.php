<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[PrivateNetwork]].
 *
 * @see PrivateNetwork
 */
class PrivateNetworkQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
