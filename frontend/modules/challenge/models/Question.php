<?php

namespace app\modules\challenge\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property int $challenge_id
 * @property string|null $name
 * @property string|null $description
 * @property float|null $points
 * @property string $player_type
 * @property string|null $code
 * @property int $weight
 * @property string $ts
 * @property int|null $parent
 *
 * @property PlayerQuestion[] $playerQuestions
 * @property Challenge $challenge
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['challenge_id'], 'required'],
            [['challenge_id', 'weight', 'parent'], 'integer'],
            [['description', 'player_type'], 'string'],
            [['points'], 'number'],
            [['ts'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 128],
            [['challenge_id', 'name'], 'unique', 'targetAttribute' => ['challenge_id', 'name']],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::className(), 'targetAttribute' => ['challenge_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'challenge_id' => 'Challenge ID',
            'name' => 'Name',
            'description' => 'Description',
            'points' => 'Points',
            'player_type' => 'Player Type',
            'code' => 'Code',
            'weight' => 'Weight',
            'ts' => 'Ts',
            'parent' => 'Parent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerQuestions()
    {
        return $this->hasMany(PlayerQuestion::className(), ['question_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswered()
    {
        return $this->hasOne(PlayerQuestion::className(), ['question_id' => 'id'])->andOnCondition(['player_id'=>Yii::$app->user->id]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallenge()
    {
        return $this->hasOne(Challenge::className(), ['id' => 'challenge_id']);
    }
}
