<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[Ondemand]].
 *
 * @see Ondemand
 */
class OndemandQuery extends \yii\db\ActiveQuery
{
    public function withExpired()
    {
      return $this->select(['*','(60*60)-TIMESTAMPDIFF(SECOND, heartbeat,now()) AS expired']);
    }
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Ondemand[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Ondemand|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
