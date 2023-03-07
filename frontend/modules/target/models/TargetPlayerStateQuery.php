<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[TargetPlayerState]].
 *
 * @see TargetPlayerState
 */
class TargetPlayerStateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetPlayerState[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetPlayerState|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
