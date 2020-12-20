<?php

namespace app\modules\tutorial\models;

use Yii;
use app\modules\target\models\Target;
/**
 * This is the model class for table "tutorial".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TutorialTarget[] $tutorialTargets
 * @property Target[] $targets
 * @property TutorialTask[] $tasks
 */
class Tutorial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tutorial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorialTargets()
    {
        return $this->hasMany(TutorialTarget::class, ['tutorial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargets()
    {
        return $this->hasMany(Target::class, ['id' => 'target_id'])->viaTable('tutorial_target', ['tutorial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(TutorialTask::class, ['tutorial_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TutorialQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TutorialQuery(get_called_class());
    }
}
