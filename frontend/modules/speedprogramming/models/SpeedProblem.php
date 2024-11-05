<?php

namespace app\modules\speedprogramming\models;

use Yii;

/**
 * This is the model class for table "speed_problem".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $challenge_image
 * @property string|null $validator_image
 * @property string|null $server
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property SpeedSolution[] $speedSolutions
 */
class SpeedProblem extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'speed_problem';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['description'], 'string'],
      [['created_at', 'updated_at'], 'safe'],
      [['name', 'challenge_image', 'validator_image', 'server'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => Yii::t('app', 'Name'),
      'description' => Yii::t('app', 'Description'),
      'challenge_image' => Yii::t('app', 'Challenge Image'),
      'validator_image' => Yii::t('app', 'Validator Image'),
      'server' => Yii::t('app', 'Server'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
    ];
  }

  /**
   * Gets query for [[SpeedSolutions]].
   *
   * @return \yii\db\ActiveQuery|SpeedSolutionQuery
   */
  public function getSpeedSolutions()
  {
    return $this->hasMany(SpeedSolution::class, ['problem_id' => 'id']);
  }

  /**
   * {@inheritdoc}
   * @return SpeedProblemQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new SpeedProblemQuery(get_called_class());
  }
  public function getDifficultyText()
  {
    return $this->difficulty;
  }
}
