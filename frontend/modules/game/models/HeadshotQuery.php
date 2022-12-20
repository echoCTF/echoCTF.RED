<?php

namespace app\modules\game\models;

/**
 * This is the ActiveQuery class for [[Headshot]].
 *
 * @see Headshot
 */
class HeadshotQuery extends \yii\db\ActiveQuery
{

    public function timed()
    {
        return $this->joinWith(['target'])->andWhere(['t.timer'=>1])->andWhere(['>','headshot.timer',0]);
    }

    public function target_avg_time($target_id)
    {
        return $this->addSelect(['*', 'avg(headshot.timer) as average'])->andWhere(['target_id'=>$target_id]);
    }
    public function player_avg_time($player_id)
    {
        return $this->addSelect(['*', 'avg(headshot.timer) as average'])->andWhere(['player_id'=>$player_id]);
    }

    public function academic($academic)
    {
        return $this->joinWith(['player'])->andWhere(['player.academic'=>$academic]);
    }

    public function mine()
    {
        return $this->andWhere(['player_id'=>\Yii::$app->user->id]);
    }

    public function last()
    {
        return $this->orderBy(['created_at'=>SORT_DESC])->limit(1);
    }

    /**
     * {@inheritdoc}
     * @return Headshot[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Headshot|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
