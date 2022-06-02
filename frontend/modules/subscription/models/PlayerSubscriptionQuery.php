<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[PlayerSubscription]].
 *
 * @see PlayerSubscription
 */
class PlayerSubscriptionQuery extends \yii\db\ActiveQuery
{
    public function me()
    {
        return $this->andWhere('[[player_id]]='.\Yii::$app->user->id);
    }
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    public function notExpired()
    {
        return $this->andWhere('[[ending]]>=NOW()');
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscription[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscription|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
