<?php

namespace app\modules\network\models;

use Yii;
use app\modules\target\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "network_target_schedule".
 *
 * @property int $id
 * @property int $target_id
 * @property int $network_id
 * @property string $migration_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Network $network
 * @property Target $target
 */
class NetworkTargetSchedule extends \app\models\ActiveRecordReadOnly
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_target_schedule';
    }

    public function behaviors()
    {
      return [
        [
          'class' => TimestampBehavior::class,
          'createdAtAttribute' => 'created_at',
          'updatedAtAttribute' => 'updated_at',
          'value' => new Expression('NOW()'),
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'migration_date'], 'required'],
            [['target_id', 'network_id'], 'integer'],
            [['migration_date'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['migration_date', 'created_at', 'updated_at'], 'safe'],
            ['network_id',  'required', 'when'=>function ($model) {
                if($model->target && $model->target->network && $model->target->networkTarget->network_id===$model->network_id)
                    $this->addError('network_id', 'The target is already on the network you are trying to schedule');
            }],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::class, 'targetAttribute' => ['network_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'network_id' => Yii::t('app', 'Network ID'),
            'migration_date' => Yii::t('app', 'Migration Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Network]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::class, ['id' => 'network_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return NetworkTargetScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NetworkTargetScheduleQuery(get_called_class());
    }
}
