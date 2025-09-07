<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[Treasure]].
 *
 * @see Target
 */
class TreasureQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Target[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Target|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
