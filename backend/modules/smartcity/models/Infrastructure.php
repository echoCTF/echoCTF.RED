<?php

namespace app\modules\smartcity\models;

use Yii;

use app\modules\gameplay\models\Target;
/**
 * This is the model class for table "infrastructure".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $ts
 *
 * @property InfrastructureTarget[] $infrastructureTargets
 * @property Target[] $targets
 */
class Infrastructure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infrastructure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['ts'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructureTargets()
    {
        return $this->hasMany(InfrastructureTarget::class, ['infrastructure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargets()
    {
        return $this->hasMany(Target::class, ['id' => 'target_id'])->viaTable('infrastructure_target', ['infrastructure_id' => 'id']);
    }
}
