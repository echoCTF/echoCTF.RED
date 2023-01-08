<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Notification]].
 *
 * @see Notification
 */
class NotificationQuery extends \yii\db\ActiveQuery
{
    public function my()
    {
       return $this->andWhere('[[player_id]]=:player_id',[':player_id'=>\Yii::$app->user->id]);
    }

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
        return $this->select(['id', 'title', 'category', 'body', 'created_at', 'archived']);
    }

    /**
     * @return Notification[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * @return Notification|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
