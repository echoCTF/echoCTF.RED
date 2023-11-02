<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[TeamAudit]].
 *
 * @see TeamAudit
 */
class TeamAuditQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TeamAudit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeamAudit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
