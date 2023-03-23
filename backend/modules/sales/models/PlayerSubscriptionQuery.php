<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[PlayerSubscription]].
 *
 * @see PlayerSubscription
 */
class PlayerSubscriptionQuery extends \yii\db\ActiveQuery
{
    public function vip()
    {
        return $this->andWhere("[[subscription_id]]='sub_vip'");
    }

    public function active($active=1)
    {
        return $this->andWhere("[[active]]=".intval($active));
    }

    public function expired($interval=240)
    {
        return $this->andWhere(['<','ending',new \yii\db\Expression("NOW() - INTERVAL $interval MINUTE")]);
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
