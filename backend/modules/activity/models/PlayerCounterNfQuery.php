<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerCounterNf]].
 *
 * @see PlayerCounterNf
 */
class PlayerCounterNfQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerCounterNf[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerCounterNf|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
