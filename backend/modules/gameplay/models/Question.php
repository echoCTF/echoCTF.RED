<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property int $challenge_id
 * @property string $name
 * @property string $description
 * @property string $points
 * @property string $player_type
 * @property string $code
 * @property int $weight
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
            [['challenge_id', 'weight'], 'integer'],
            [['description', 'player_type'], 'string'],
            [['points'], 'number'],
            ['player_type', 'in', 'range' => ['offense', 'defense','both']],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 128],
            [['challenge_id', 'name'], 'unique', 'targetAttribute' => ['challenge_id', 'name']],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::class, 'targetAttribute' => ['challenge_id' => 'id']],
            [['weight','points'], 'default', 'value'=> 0],
            [['player_type'],'default', 'value'=>'offense'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerQuestions()
    {
        return $this->hasMany(\app\modules\activity\models\PlayerQuestion::class, ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallenge()
    {
        return $this->hasOne(Challenge::class, ['id' => 'challenge_id']);
    }

}
