<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerDisconnectQueue]].
 *
 * @see PlayerDisconnectQueue
 */
class PlayerDisconnectQueueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerDisconnectQueue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerDisconnectQueue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
