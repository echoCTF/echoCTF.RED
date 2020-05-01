<?php

namespace app\modules\game\models;

/**
 * This is the ActiveQuery class for [[Badge]].
 *
 * @see Badge
 */
class BadgeQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * return Badge[]|array
     */
    public function received_by(int $player_id)
    {
        return $this->andWhere('id in (SELECT badge_id FROM player_badge WHERE player_id='.$player_id.')');
    }

    /**
     * {@inheritdoc}
     * @return Badge[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Badge|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
