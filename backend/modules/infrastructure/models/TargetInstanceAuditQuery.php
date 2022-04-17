<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetInstanceAudit]].
 *
 * @see TargetInstanceAudit
 */
class TargetInstanceAuditQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetInstanceAudit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetInstanceAudit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
