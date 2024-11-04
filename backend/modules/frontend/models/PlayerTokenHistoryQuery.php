<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[PlayerTokenHistory]].
 *
 * @see PlayerTokenHistory
 */
class PlayerTokenHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerTokenHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTokenHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
