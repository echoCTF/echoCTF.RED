<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[WsTokenHistory]].
 *
 * @see WsTokenHistory
 */
class WsTokenHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return WsTokenHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WsTokenHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
