<?php

namespace app\modules\sales\models;

/**
 * This is the ActiveQuery class for [[StripeWebhook]].
 *
 * @see StripeWebhook
 */
class StripeWebhookQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return StripeWebhook[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return StripeWebhook|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
