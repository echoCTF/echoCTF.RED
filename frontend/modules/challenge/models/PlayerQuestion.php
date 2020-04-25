<?php

namespace app\modules\challenge\models;

use Yii;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_question".
 *
 * @property int $id
 * @property int $question_id
 * @property int|null $player_id
 * @property float|null $points
 * @property string $ts
 *
 * @property Question $question
 * @property Player $player
 */
class PlayerQuestion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['question_id', 'player_id'], 'integer'],
            [['points'], 'number'],
            [['ts'], 'safe'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'player_id' => 'Player ID',
            'points' => 'Points',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }
}
