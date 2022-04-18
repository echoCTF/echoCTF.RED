<?php

namespace app\modules\target\models;

use Yii;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "target_metadata".
 *
 * @property int $target_id
 * @property string|null $scenario
 * @property string|null $instructions
 * @property string|null $solution
 * @property string|null $pre_credits
 * @property string|null $post_credits
 * @property string|null $pre_exploitation
 * @property string|null $post_exploitation
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Target $target
 */
class TargetMetadata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_metadata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scenario', 'instructions', 'solution', 'pre_credits', 'post_credits', 'pre_exploitation', 'post_exploitation'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'scenario' => 'Scenario',
            'instructions' => 'Instructions',
            'solution' => 'Solution',
            'pre_credits' => 'Pre Credits',
            'post_credits' => 'Post Credits',
            'pre_exploitation' => 'Pre Exploitation',
            'post_exploitation' => 'Post Exploitation',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * @return TargetMetadataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetMetadataQuery(get_called_class());
    }

    public function save($runValidation=true, $attributeNames=null)
    {
      throw new \LogicException("Saving is disabled for this model.");
    }

}
