<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Notification]].
 *
 * @see Notification
 */
class NotificationQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        return $this->andWhere('[[archived]]=0');
    }
    public function forPlayer($player_id)
    {
        return $this->andWhere(['player_id'=>$player_id]);
    }
    public function forAjax()
    {
        return $this->select(['id','title','created_at','archived']);
    }

    /**
     * {@inheritdoc}
     * @return Notification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Notification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
