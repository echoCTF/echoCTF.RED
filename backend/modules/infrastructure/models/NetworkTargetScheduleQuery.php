<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[NetworkTargetSchedule]].
 *
 * @see NetworkTargetSchedule
 */
class NetworkTargetScheduleQuery extends \yii\db\ActiveQuery
{
    public function queue()
    {
        return $this->andWhere('migration_date <= NOW()');
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
