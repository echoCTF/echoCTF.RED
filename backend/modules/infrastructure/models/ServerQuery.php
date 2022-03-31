<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[Server]].
 *
 * @see Server
 */
class ServerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Server[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Server|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
