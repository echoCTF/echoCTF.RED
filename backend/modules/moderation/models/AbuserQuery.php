<?php

namespace app\modules\moderation\models;

/**
 * This is the ActiveQuery class for [[Abuser]].
 *
 * @see Abuser
 */
class AbuserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Abuser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Abuser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
