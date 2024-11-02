<?php

namespace app\modules\team\models;

/**
 * This is the ActiveQuery class for [[TeamInvite]].
 *
 * @see TeamInvite
 */
class TeamInviteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TeamInvite[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeamInvite|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
