<?php

namespace app\modules\gameplay\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\activity\models\PlayerHint;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "hint".
 *
 * @property int $id
 * @property string $title
 * @property string $player_type
 * @property string $message
 * @property string $category
 * @property int $badge_id Display this record after the user received the badge_id
 * @property int $finding_id Display this record after the user received the finding_id
 * @property int $treasure_id Display this record after the user received the treasure_id
 * @property int $question_id Display this record after the user answered the question_id
 * @property int $points_user Display this record after the user reaches these many points
 * @property int $points_team Display this record after the team reaches these many points
 * @property int $timeafter Display this hint after X seconds have been passed since the Start of the event
 * @property int $active Set this hint as active or innactive
 * @property string $ts
 *
 * @property Badge $badge
 * @property Finding $finding
 * @property Treasure $treasure
 * @property Question $question
 * @property PlayerHint[] $playerHints
 * @property Player[] $players
 */
class Hint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hint';
    }

    public function behaviors()
    {
      return [
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'ts',
              'updatedAtAttribute' => 'ts',
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
            [['title'], 'required'],
            [['player_type', 'message'], 'string'],
            [['badge_id', 'finding_id', 'treasure_id', 'points_user', 'points_team', 'timeafter', 'active'], 'integer'],
            [['title', 'category'], 'string', 'max' => 255],
            //[['title'], 'unique'],
            [['badge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Badge::class, 'targetAttribute' => ['badge_id' => 'id']],
            [['finding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Finding::class, 'targetAttribute' => ['finding_id' => 'id']],
            [['treasure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treasure::class, 'targetAttribute' => ['treasure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'player_type' => 'Player Type',
            'message' => 'Message',
            'category' => 'Category',
            'badge_id' => 'Badge ID',
            'finding_id' => 'Finding ID',
            'treasure_id' => 'Treasure ID',
            'points_user' => 'Points User',
            'points_team' => 'Points Team',
            'timeafter' => 'Timeafter',
            'active' => 'Active',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadge()
    {
        return $this->hasOne(Badge::class, ['id' => 'badge_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinding()
    {
        return $this->hasOne(Finding::class, ['id' => 'finding_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasure()
    {
        return $this->hasOne(Treasure::class, ['id' => 'treasure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerHints()
    {
        return $this->hasMany(PlayerHint::class, ['hint_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('user_hint', ['hint_id' => 'id']);
    }
}
