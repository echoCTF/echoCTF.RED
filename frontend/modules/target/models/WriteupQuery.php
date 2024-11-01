<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[Writeup]].
 *
 * @see Writeup
 */
class WriteupQuery extends \yii\db\ActiveQuery
{
    public function approved()
    {
        return $this->andWhere('approved=1');
    }
    public function active()
    {
        return $this->joinWith('player')->andWhere(['player.status'=>\app\models\Player::STATUS_ACTIVE]);
    }
    public function totals()
    {
        return $this->addSelect(['writeup.*','COUNT(*) AS cnt'])->approved()->active()->groupBy(['player_id'])->orderBy(['cnt' => SORT_DESC,'player_id'=>SORT_ASC]);
    }
    /**
     * {@inheritdoc}
     * @return Writeup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Writeup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
