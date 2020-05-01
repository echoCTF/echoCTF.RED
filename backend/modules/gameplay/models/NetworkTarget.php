<?php

namespace app\modules\gameplay\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "network_target".
 *
 * @property int $network_id
 * @property int $target_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Network $network
 * @property Target $target
 */
class NetworkTarget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['network_id', 'target_id'], 'required'],
            [['network_id', 'target_id', 'weight'], 'integer'],
            [['weight'], 'default', 'value'=>0],
            [['created_at', 'updated_at'], 'safe'],
            [['network_id', 'target_id'], 'unique', 'targetAttribute' => ['network_id', 'target_id']],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::class, 'targetAttribute' => ['network_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'network_id' => 'Network ID',
            'target_id' => 'Target ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::class, ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }
}
