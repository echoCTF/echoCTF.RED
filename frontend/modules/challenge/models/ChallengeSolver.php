<?php

namespace app\modules\challenge\models;

use Yii;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_question".
 *
 * @property int $id
 * @property int $challenge_id
 * @property int|null $player_id
 * @property float|null $points
 * @property int $timer
 * @property int|null $rating
 * @property boolean $first
 * @property string $ts
 *
 * @property Challenge $challenge
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

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'challenge_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'timer' =>  AttributeTypecastBehavior::TYPE_INTEGER,
                    'rating' =>  AttributeTypecastBehavior::TYPE_INTEGER,
                    'first' =>  AttributeTypecastBehavior::TYPE_BOOLEAN,
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
            [['challenge_id','player_id'], 'required'],
            [['challenge_id', 'player_id'], 'integer'],
            [['timer','rating'], 'integer'],
            [['rating'], 'default','value'=>-1],
            ['rating','in','range'=>[-1,0,1,2,3,4,5,6]],
            [['first'], 'boolean'],
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
