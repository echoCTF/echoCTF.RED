<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "target_volume".
 *
 * @property int $target_id Docker this volume belongs to
 * @property string $volume Volume on host to map
 * @property string $bind Bind to path within pod
 *
 * @property Target $target
 */
class TargetVolume extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_volume';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'volume', 'bind'], 'required'],
            [['target_id'], 'integer'],
            [['volume', 'bind'], 'string', 'max' => 255],
            [['target_id', 'volume'], 'unique', 'targetAttribute' => ['target_id', 'volume']],
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
            'volume' => 'Volume',
            'bind' => 'Bind',
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
