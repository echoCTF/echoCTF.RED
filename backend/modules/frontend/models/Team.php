<?php

namespace app\modules\frontend\models;

use Yii;
use app\modules\activity\models\TeamStream;

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
 * @property int $inviteonly
 * @property boolean $locked
 * @property string $recruitment
 *
 * @property Player $owner
 * @property TeamPlayer[] $teamPlayers
 * @property Player[] $players
 */
class Team extends \yii\db\ActiveRecord
{
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
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'insertTeam']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'owner_id'], 'required'],
            [['description', 'logo'], 'string'],
            [['academic', 'owner_id','inviteonly'], 'integer'],
            [['inviteonly','locked'], 'boolean'],
            [['inviteonly'], 'default','value'=>true],
            [['locked'], 'default','value'=>false],
            [['name','recruitment'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 30],
            [['token'], 'default', 'value' => substr(Yii::$app->security->generateRandomString(), 0, 30)],
            [['name'], 'unique'],
            [['token'], 'unique'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['owner_id' => 'id']],
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
            'inviteonly'=>'Invite only',
            'locked'=>'Locked'
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

    public function getRank()
    {
      if($this->hasOne(\app\modules\activity\models\TeamRank::class, ['id' => 'id'])->one()!==null)
        return $this->hasOne(\app\modules\activity\models\TeamRank::class, ['id' => 'id']);
      else
        return new \app\modules\activity\models\TeamRank;
    }

    public function getScore()
    {
        return $this->hasOne(\app\modules\activity\models\TeamScore::class, ['team_id' => 'id']);
    }

    public static function insertTeam($event)
    {
      Yii::$app->db
      ->createCommand("INSERT INTO team_player (team_id,player_id,approved) values (:tid,:pid,:approved)")
      ->bindValue(':tid',$event->sender->id)
      ->bindValue(':pid',$event->sender->owner_id)
      ->bindValue(':approved',1)
      ->execute();
    }

    public function getAcademicLong()
    {
        switch($this->academic)
        {
          case 0:
            return "government";
          case 1:
            return "education";
          default:
            return "professional";
        }
    }

    public function getAcademicShort()
    {
      switch($this->academic)
      {
        case 0:
          return "gov";
        case 1:
          return "edu";
        default:
          return "pro";
      }
    }
    public function getUcademicLong()
    {
        return ucfirst($this->academicLong);
    }

    public function getUcacademicShort()
    {
        return ucfirst($this->academicShort);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreams()
    {
        return $this->hasMany(TeamStream::class, ['team_id' => 'id'])->orderBy(['ts'=>SORT_DESC]);
    }
}
