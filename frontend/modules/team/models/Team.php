<?php

namespace app\modules\team\models;

use Yii;
use app\models\Player;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $academic
 * @property resource $logo
 * @property int $owner_id
 * @property string $token
 *
 * @property Player $owner
 * @property TeamPlayer[] $teamPlayers
 * @property Player[] $players
 */
class Team extends \yii\db\ActiveRecord
{
  public $uploadedAvatar;
  const SCENARIO_CREATE = 'create';
  const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'createScore']);
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'description','token'],
            self::SCENARIO_UPDATE => ['name', 'description', 'uploadedAvatar'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'owner_id'], 'required'],
            [['description', 'logo'], 'string'],
            [['academic', 'owner_id'], 'integer'],
            [['academic'], 'boolean'],
            [['name'], 'trim'],
            [['name'], 'string', 'length' => [3, 255]],
            [['token'], 'string', 'max' => 30],
            [['token'], 'default', 'value' => Yii::$app->security->generateRandomString(10)],
            [['name'], 'unique',  'when' => function($model, $attribute) {
                return $model->{$attribute} !== $model->getOldAttribute($attribute);
            }],
            [['token'], 'unique'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => 'File larger than expected, limit is 500KB'],
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
            'description' => 'Description',
            'academic' => 'Academic',
            'logo' => 'Logo',
            'owner_id' => 'Owner ID',
            'token' => 'Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::class, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScore()
    {
        return $this->hasOne(TeamScore::class, ['team_id' => 'id']);
    }

    public function getRank()
    {
        return $this->hasOne(TeamRank::class, ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('team_player', ['team_id' => 'id']);
    }

    public function getValidLogo()
    {
      if($this->logo===NULL)
        return '../../defense.png';
      return $this->logo;
    }


    public static function find()
    {
        return new TeamQuery(get_called_class());
    }

    public function createScore($event)
    {
      $ts=new TeamScore();
      $ts->team_id=$this->id;
      $ts->save();
    }
}
