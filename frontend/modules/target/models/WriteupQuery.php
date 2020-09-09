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
