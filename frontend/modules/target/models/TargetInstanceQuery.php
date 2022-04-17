<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[TargetInstance]].
 *
 * @see TargetInstance
 */
class TargetInstanceQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[ip]] IS NOT NULL')->andWhere('[[reboot]]!=2');
    }

    public function pending_action($minutes_ago=60)
    {
        return $this->andWhere('[[ip]] IS NULL')->orWhere('[[reboot]]>0')->orWhere(['<','updated_at',new \yii\db\Expression("NOW() - INTERVAL $minutes_ago MINUTE")]);
    }

    /**
     * {@inheritdoc}
     * @return TargetInstance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetInstance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
