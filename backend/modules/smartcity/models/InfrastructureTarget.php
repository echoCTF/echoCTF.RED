<?php

namespace app\modules\smartcity\models;

use Yii;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "infrastructure_target".
 *
 * @property int $infrastructure_id
 * @property int $target_id
 *
 * @property Infrastructure $infrastructure
 * @property Target $target
 */
class InfrastructureTarget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infrastructure_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['infrastructure_id', 'target_id'], 'required'],
            [['infrastructure_id', 'target_id'], 'integer'],
            [['target_id'], 'unique'],
            [['infrastructure_id', 'target_id'], 'unique', 'targetAttribute' => ['infrastructure_id', 'target_id']],
            [['infrastructure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Infrastructure::class, 'targetAttribute' => ['infrastructure_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'infrastructure_id' => 'infrastructure ID',
            'target_id' => 'Target ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getinfrastructure()
    {
        return $this->hasOne(Infrastructure::class, ['id' => 'infrastructure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }
}
