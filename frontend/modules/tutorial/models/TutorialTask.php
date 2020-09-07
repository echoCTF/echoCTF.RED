<?php

namespace app\modules\tutorial\models;

use Yii;

/**
 * This is the model class for table "tutorial_task".
 *
 * @property int $id
 * @property int|null $tutorial_id
 * @property string|null $title
 * @property string|null $description
 * @property int|null $points
 * @property string|null $answer
 * @property int|null $weight
 *
 * @property Tutorial $tutorial
 * @property TutorialTaskDependency[] $tutorialTaskDependencies
 */
class TutorialTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tutorial_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tutorial_id', 'points', 'weight'], 'integer'],
            [['description'], 'string'],
            [['title', 'answer'], 'string', 'max' => 255],
            [['tutorial_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tutorial::class, 'targetAttribute' => ['tutorial_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tutorial_id' => Yii::t('app', 'Tutorial ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'points' => Yii::t('app', 'Points'),
            'answer' => Yii::t('app', 'Answer'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorial()
    {
        return $this->hasOne(Tutorial::class, ['id' => 'tutorial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorialTaskDependencies()
    {
        return $this->hasMany(TutorialTaskDependency::class, ['tutorial_task_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TutorialTaskQuery(get_called_class());
    }
}
