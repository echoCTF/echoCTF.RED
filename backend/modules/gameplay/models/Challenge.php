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
 * @property object $file The file upload handler
 * @property boolean $active Is challenge active?
 * @property boolean $timer Keep timer for solving?
 * @property boolean $public Is challenge public?
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
            [['description', 'player_type','icon'], 'string'],
            [['active','timer','public'], 'default','value'=>true],
            [['active','timer','public'], 'boolean'],
            [['file'], 'file'],
            [['name', 'category', 'difficulty', 'filename'], 'string', 'max' => 255],
            [['filename'], 'trim'],
            [['name'], 'unique'],
            [['player_type'],'default', 'value'=>'offense'],
            ['player_type', 'in', 'range' => ['offense', 'defense','both']],
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
        return $this->hasMany(Question::class, ['challenge_id' => 'id'])->orderBy(['weight' => SORT_ASC,'id'=>SORT_ASC]);;
    }
}
