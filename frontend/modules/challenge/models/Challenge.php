<?php

namespace app\modules\challenge\models;

use Yii;

/**
 * This is the model class for table "challenge".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $category
 * @property string|null $difficulty
 * @property string|null $description
 * @property string $player_type
 * @property string|null $filename The filename that will be provided to participants
 * @property string $ts
 *
 * @property Question[] $questions
 */
class Challenge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'challenge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'player_type'], 'string'],
            [['ts'], 'safe'],
            [['name', 'category', 'difficulty', 'filename'], 'string', 'max' => 255],
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
            'category' => 'Category',
            'difficulty' => 'Difficulty',
            'description' => 'Description',
            'player_type' => 'Player Type',
            'filename' => 'Filename',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['challenge_id' => 'id']);
    }
}
