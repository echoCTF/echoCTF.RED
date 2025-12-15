<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[WsToken]].
 *
 * @see WsToken
 */
class WsTokenQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return WsToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WsToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
