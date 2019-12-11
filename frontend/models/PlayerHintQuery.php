<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerHint]].
 *
 * @see PlayerHint
 */
class PlayerHintQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        return $this->andWhere('[[status]]=1');
    }
    public function forPlayer($player_id)
    {
        return $this->andWhere(['player_id'=>$player_id]);
    }
    public function forAjax()
    {
        return $this->select(['player_hint.*','hint.title']);

    }

    /**
     * {@inheritdoc}
     * @return PlayerHint[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerHint|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
