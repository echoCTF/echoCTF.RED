<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerDisconnectQueueHistory]].
 *
 * @see PlayerDisconnectQueueHistory
 */
class PlayerDisconnectQueueHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerDisconnectQueueHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerDisconnectQueueHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
