<?php

namespace app\modules\network\models;

/**
 * This is the ActiveQuery class for [[NetworkPlayer]].
 *
 * @see NetworkPlayer
 */
class NetworkPlayerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NetworkPlayer[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NetworkPlayer|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
