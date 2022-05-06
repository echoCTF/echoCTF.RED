<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerScoreMonthly]].
 *
 * @see PlayerScoreMonthly
 */
class PlayerScoreMonthlyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerScoreMonthly[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerScoreMonthly|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
