<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[PlayerPaymentHistory]].
 *
 * @see PlayerPaymentHistory
 */
class PlayerPaymentHistoryQuery extends \yii\db\ActiveQuery
{
    public function mine()
    {
        return $this->andWhere(['player_id'=>\Yii::$app->user->id]);
    }

    /**
     * {@inheritdoc}
     * @return PlayerPaymentHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerPaymentHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
