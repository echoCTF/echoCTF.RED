<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerSpin]].
 *
 * @see PlayerSpin
 */
class PlayerSpinQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerSpin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSpin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
