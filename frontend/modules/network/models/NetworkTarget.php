<?php

namespace app\modules\network\models;

use Yii;
use \app\modules\target\models\Target;
/**
 * This is the model class for table "network_target".
 *
 * @property int $network_id
 * @property int $target_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $weight
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
            [['created_at', 'updated_at'], 'safe'],
            [['network_id', 'target_id'], 'unique', 'targetAttribute' => ['network_id', 'target_id']],
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
            'network_id' => 'Network ID',
            'target_id' => 'Target ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'weight' => 'Weight',
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

    /**
     * {@inheritdoc}
     * @return NetworkTargetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NetworkTargetQuery(get_called_class());
    }
}
