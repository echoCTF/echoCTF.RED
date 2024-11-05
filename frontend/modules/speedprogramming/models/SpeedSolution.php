<?php

namespace app\modules\speedprogramming\models;

use Yii;
use app\models\Player;
use app\modules\team\models\Team;
use app\modules\target\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "speed_solution".
 *
 * @property int $id
 * @property int $player_id
 * @property int $problem_id
 * @property string|null $language
 * @property resource|null $sourcecode
 * @property string|null $status
 * @property int|null $points
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Problem $problem
 */
class SpeedSolution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'speed_solution';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'problem_id'], 'required'],
            [['player_id', 'problem_id', 'points'], 'integer'],
            [['sourcecode'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['problem_id'], 'exist', 'skipOnError' => true, 'targetClass' => SpeedProblem::class, 'targetAttribute' => ['problem_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'problem_id' => 'Problem ID',
            'language' => 'Language',
            'sourcecode' => 'Sourcecode',
            'status' => 'Status',
            'points' => 'Points',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Problem]].
     *
     * @return \yii\db\ActiveQuery|SpeedProblemQuery
     */
    public function getProblem()
    {
        return $this->hasOne(SpeedProblem::class, ['id' => 'problem_id']);
    }

    /**
     * {@inheritdoc}
     * @return SpeedSolutionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SpeedSolutionQuery(get_called_class());
    }
}
