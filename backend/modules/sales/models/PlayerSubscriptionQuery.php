<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[PlayerSubscription]].
 *
 * @see PlayerSubscription
 */
class PlayerSubscriptionQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscription[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscription|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
