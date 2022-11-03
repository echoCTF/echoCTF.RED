<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "target_variable".
 *
 * @property int $target_id Docker this variable belongs to
 * @property string $key Variable key (aka name)
 * @property string $val Variable value
 *
 * @property Target $target
 */
class TargetVariable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_variable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'key'], 'required'],
            [['target_id'], 'integer'],
            [['key', 'val'], 'string', 'max' => 255],
            [['target_id', 'key'], 'unique', 'targetAttribute' => ['target_id', 'key']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'target_id' => 'Target ID',
            'key' => 'Key',
            'val' => 'Val',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }
}
