<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\activity\models\PlayerTutorialTask;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "tutorial_task_dependency".
 *
 * @property int $id
 * @property int|null $tutorial_task_id
 * @property int $item_id
 * @property string|null $item
 *
 * @property PlayerTutorialTask[] $playerTutorialTasks
 * @property Player[] $players
 * @property TutorialTask $tutorialTask
 */
class TutorialTaskDependency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tutorial_task_dependency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tutorial_task_id', 'item_id'], 'integer'],
            [['item_id'], 'required'],
            [['item'], 'string', 'max' => 255],
            [['tutorial_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => TutorialTask::class, 'targetAttribute' => ['tutorial_task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tutorial_task_id' => Yii::t('app', 'Tutorial Task ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'item' => Yii::t('app', 'Item'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerTutorialTasks()
    {
        return $this->hasMany(PlayerTutorialTask::class, ['tutorial_task_dependency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('player_tutorial_task', ['tutorial_task_dependency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorialTask()
    {
        return $this->hasOne(TutorialTask::class, ['id' => 'tutorial_task_id']);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTaskDependencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TutorialTaskDependencyQuery(get_called_class());
    }
}
