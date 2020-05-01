<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerSsl]].
 *
 * @see PlayerSsl
 */
class PlayerSslQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerSsl[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSsl|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
