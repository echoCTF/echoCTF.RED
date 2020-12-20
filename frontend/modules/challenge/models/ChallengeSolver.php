<?php

namespace app\modules\challenge\models;

use Yii;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_question".
 *
 * @property int $id
 * @property int $challenge_id
 * @property int|null $player_id
 * @property float|null $points
 * @property int timer
 * @property string $ts
 *
 * @property Challenge[] $challenge
 * @property Player[] $player
 */
class ChallengeSolver extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'challenge_solver';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['challenge_id','player_id'], 'required'],
            [['challenge_id', 'player_id'], 'integer'],
            [['timer','rating'], 'integer'],
            [['crated_at'], 'safe'],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::class, 'targetAttribute' => ['challenge_id' => 'id']],
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
            'challenge_id' => 'Challenge ID',
            'player_id' => 'Player ID',
            'timer' => 'Timer',
            'rating' => 'Rating',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallenge()
    {
        return $this->hasOne(Challenge::class, ['id' => 'challenge_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return ChallengeSolverQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChallengeSolverQuery(get_called_class());
    }


}
