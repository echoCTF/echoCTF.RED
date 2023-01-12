<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
/**
 * This is the model class for table "headshot".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int $timer
 * @property string|null $created_at
 *
 * @property Player $player
 * @property Target $target
 */
class Headshot extends \yii\db\ActiveRecord
{
    private $ratings=[
        -1=>"unrated",
        0=>"beginner",
        1=>"basic",
        2=>"intermediate",
        3=>"advanced",
        4=>"expert",
        5=>"guru",
        6=>"insane"
      ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'headshot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id', 'timer', 'rating'], 'integer'],
            [['first'], 'boolean'],
            [['first'], 'default','value'=>0],
            [['created_at', 'rating', 'timer'], 'safe'],
            [['player_id', 'target_id'], 'unique', 'targetAttribute' => ['player_id', 'target_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * @return HeadshotQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HeadshotQuery(get_called_class());
    }

    public function getRatingString()
    {
      return $this->ratings[$this->rating];
    }

    public function getRatings()
    {
      return $this->ratings;
    }

}
