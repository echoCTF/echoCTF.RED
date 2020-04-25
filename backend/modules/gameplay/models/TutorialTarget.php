<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "tutorial_target".
 *
 * @property int $tutorial_id
 * @property int $target_id
 * @property int|null $weight
 *
 * @property Target $target
 * @property Tutorial $tutorial
 */
class TutorialTarget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tutorial_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tutorial_id', 'target_id'], 'required'],
            [['tutorial_id', 'target_id', 'weight'], 'integer'],
            [['tutorial_id', 'target_id'], 'unique', 'targetAttribute' => ['tutorial_id', 'target_id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
            [['tutorial_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tutorial::class, 'targetAttribute' => ['tutorial_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tutorial_id' => Yii::t('app', 'Tutorial ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorial()
    {
        return $this->hasOne(Tutorial::class, ['id' => 'tutorial_id']);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTargetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TutorialTargetQuery(get_called_class());
    }
}
