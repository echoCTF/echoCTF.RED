<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Hint]].
 *
 * @see Hint
 */
class HintQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Hint[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Hint|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
