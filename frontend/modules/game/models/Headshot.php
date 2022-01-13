<?php

namespace app\modules\game\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\models\Player;
use app\modules\target\models\Target;
use app\modules\target\models\Writeup;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "headshot".
 *
 * @property int $player_id
 * @property int $target_id
 * @property string|null $created_at
 * @property int $timer
 * @property int $rating
 * @property boolean $first
 * @property string|null $rated
 *
 * @property Player $player
 * @property Target $target
 * @property Writeup $writeup
 */
class Headshot extends \yii\db\ActiveRecord
{
  public $average=0;
  public $difficulties=[
    "beginner",
    "basic",
    "intermediate",
    "advanced",
    "expert",
    "guru",
    "insane",
  ];

  public function behaviors()
  {
      return [
          'typecast' => [
              'class' => AttributeTypecastBehavior::class,
              'attributeTypes' => [
                  'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'timer' =>  AttributeTypecastBehavior::TYPE_INTEGER,
                  'rating'=>  AttributeTypecastBehavior::TYPE_INTEGER,
                  'first' =>  AttributeTypecastBehavior::TYPE_BOOLEAN,
              ],
              'typecastAfterValidate' => true,
              'typecastBeforeSave' => true,
              'typecastAfterFind' => true,
        ],
        [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'created_at',
              'value' => new Expression('NOW()'),
        ],
      ];
  }

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
            [['player_id', 'target_id', 'timer','rating'], 'integer'],
            [['rating'], 'default','value'=>-1],
            ['rating','in','range'=>[-1,0,1,2,3,4,5,6]],
            [['first'], 'boolean'],
            [['created_at', 'timer'], 'safe'],
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
            'timer' => Yii::t('app', 'Timer'),
            'first' => Yii::t('app', 'First'),
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
    public function getProfile()
    {
        return $this->hasOne(\app\models\Profile::class, ['player_id' => 'player_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWriteup()
    {
        return $this->hasOne(Writeup::class, ['player_id' => 'player_id','target_id'=>'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return HeadshotQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HeadshotQuery(get_called_class());
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

    /**
     * Return rating name instead of number
     * @return string|null the rating name
     */
    public function getRated()
    {
      if($this->rating<0) return null;
      return $this->difficulties[$this->rating];
    }
}
