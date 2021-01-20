<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[Target]].
 *
 * @see Target
 */
class FindingQuery extends \yii\db\ActiveQuery
{
//    public function poweredup()
//    {
//        return $this->andWhere('(finding.target_id IN (SELECT target_ondemand.target_id FROM target_ondemand WHERE state=1) or (SELECT count(*) FROM target_ondemand where target_ondemand.target_id=finding.target_id)=0)');
//    }

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
