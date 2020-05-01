<?php

namespace app\modules\gameplay\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "challenge".
 *
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string $difficulty
 * @property string $description
 * @property string $player_type
 * @property string $filename The filename that will be provided to participants
 * @property string $file The filename that will be provided to participants
 *
 * @property Question[] $questions
 */
class Challenge extends \yii\db\ActiveRecord
{
  public $file;
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
            [['file'], 'file'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::class, ['challenge_id' => 'id']);
    }
}
