<?php

namespace app\modules\tutorial\models;

use Yii;
use app\models\Player;

/**
 * This is the model class for table "player_tutorial_task".
 *
 * @property int $player_id
 * @property int $tutorial_task_dependency_id
 * @property int $points
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property TutorialTaskDependency $tutorialTaskDependency
 */
class PlayerTutorialTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_tutorial_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'tutorial_task_dependency_id'], 'required'],
            [['player_id', 'tutorial_task_dependency_id', 'points'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['player_id', 'tutorial_task_dependency_id'], 'unique', 'targetAttribute' => ['player_id', 'tutorial_task_dependency_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['tutorial_task_dependency_id'], 'exist', 'skipOnError' => true, 'targetClass' => TutorialTaskDependency::class, 'targetAttribute' => ['tutorial_task_dependency_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'tutorial_task_dependency_id' => Yii::t('app', 'Tutorial Task Dependency ID'),
            'points' => Yii::t('app', 'Points'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTutorialTaskDependency()
    {
        return $this->hasOne(TutorialTaskDependency::class, ['id' => 'tutorial_task_dependency_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTutorialTaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerTutorialTaskQuery(get_called_class());
    }
}
