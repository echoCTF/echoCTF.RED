<?php

namespace app\modules\network\models;

/**
 * This is the ActiveQuery class for [[NetworkTargetSchedule]].
 *
 * @see NetworkTargetSchedule
 */
class NetworkTargetScheduleQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        return $this->andWhere('migration_date >= NOW()')->orderBy(['migration_date'=>SORT_ASC,'network_id'=>SORT_ASC]);
    }

    /**
     * {@inheritdoc}
     * @return NetworkTargetSchedule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NetworkTargetSchedule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
