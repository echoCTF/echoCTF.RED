<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerToken]].
 *
 * @see PlayerToken
 */
class PlayerTokenQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
