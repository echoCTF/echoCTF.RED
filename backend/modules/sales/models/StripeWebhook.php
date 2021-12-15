<?php

namespace app\modules\sales\models;

use Yii;

/**
 * This is the model class for table "stripe_webhook".
 *
 * @property int $id
 * @property string $type
 * @property string|null $object
 * @property string|null $object_id
 * @property string $ts
 */
class StripeWebhook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stripe_webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['object','object_id'], 'string'],
            [['ts'], 'safe'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'object' => Yii::t('app', 'Object'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return StripeWebhookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StripeWebhookQuery(get_called_class());
    }
}
