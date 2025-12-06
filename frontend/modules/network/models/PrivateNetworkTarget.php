<?php

namespace app\modules\network\models;
use app\modules\target\models\Target;

use Yii;

/**
 * This is the model class for table "private_network_target".
 *
 * @property int $id
 * @property int|null $private_network_id
 * @property int|null $target_id
 *
 * @property PrivateNetwork $privateNetwork
 * @property Target $target
 */
class PrivateNetworkTarget extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'private_network_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['private_network_id', 'target_id'], 'default', 'value' => null],
            [['private_network_id', 'target_id'], 'integer'],
            [['private_network_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrivateNetwork::class, 'targetAttribute' => ['private_network_id' => 'id']],
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
            'private_network_id' => Yii::t('app', 'Private Network ID'),
            'target_id' => Yii::t('app', 'Target ID'),
        ];
    }

    /**
     * Gets query for [[PrivateNetwork]].
     *
     * @return \yii\db\ActiveQuery|PrivateNetworkQuery
     */
    public function getPrivateNetwork()
    {
        return $this->hasOne(PrivateNetwork::class, ['id' => 'private_network_id']);
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
     * @return PrivateNetworkTargetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PrivateNetworkTargetQuery(get_called_class());
    }

}
