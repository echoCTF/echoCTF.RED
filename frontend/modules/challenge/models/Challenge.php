<?php

namespace app\modules\challenge\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

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
 * @property bool $timer
 *
 * @property Question[] $questions
 */
class Challenge extends \yii\db\ActiveRecord
{
    public $total_questions,
          $player_answers;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'challenge';
    }

    public function behaviors()
    {
        return [
          'typecast' => [
              'class' => AttributeTypecastBehavior::class,
              'attributeTypes' => [
                  'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'total_questions' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'player_answers' => AttributeTypecastBehavior::TYPE_INTEGER,
              ],
              'typecastAfterValidate' => true,
              'typecastBeforeSave' => true,
              'typecastAfterFind' => true,
          ],
        ];
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
        return $this->hasMany(Question::class, ['challenge_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ChallengeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChallengeQuery(get_called_class());
    }

    public function getCompleted(): bool
    {
      if(\Yii::$app->user->isGuest) return false;
      return ChallengeSolver::find()->where(['challenge_id' => $this->id,'player_id'=>\Yii::$app->user->id])->exists();
    }

    public function getPoints()
    {
      $sum_points=0;
      foreach($this->questions as $tr)
      {
        $sum_points+=$tr->points;
      }
      return $sum_points;
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException(\Yii::t('app',"Saving is disabled for this model."));
    }

}
