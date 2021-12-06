<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[PlayerDisabledroute]].
 *
 * @see PlayerDisabledroute
 */
class PlayerDisabledrouteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerDisabledroute[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerDisabledroute|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
