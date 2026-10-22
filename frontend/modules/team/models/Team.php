<?php

namespace app\modules\team\models;

use Yii;
use app\models\Player;
use yii\base\UserException;
use yii\web\UploadedFile;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $academic
 * @property string $logo
 * @property int $owner_id
 * @property string $token
 * @property boolean $inviteonly
 * @property boolean $locked
 * @property string $recruitment
 *
 * @property Player $owner
 * @property TeamPlayer[] $teamPlayers
 * @property Player[] $players
 * @property TeamInvite $inviteOrCreate
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
  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'id' => AttributeTypecastBehavior::TYPE_INTEGER,
          'inviteonly' => AttributeTypecastBehavior::TYPE_BOOLEAN,
          'locked' => AttributeTypecastBehavior::TYPE_BOOLEAN,
          'academic' => AttributeTypecastBehavior::TYPE_INTEGER,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
    ];
  }

  public function init()
  {
    parent::init();
    $this->on(self::EVENT_AFTER_INSERT, [$this, 'createScore']);
  }

  public function scenarios()
  {
    return [
      self::SCENARIO_CREATE => ['name', 'description', 'token', 'academic'],
      self::SCENARIO_UPDATE => ['name', 'description', 'uploadedAvatar', 'inviteonly', 'recruitment', 'locked'],
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
      [['inviteonly', 'locked'], 'boolean'],
      [['inviteonly'], 'default', 'value' => true],
      [['locked'], 'default', 'value' => false],
      [['name'], 'trim'],
      [['name'], 'string', 'length' => [3, 32]],
      [['description', 'recruitment'], 'string', 'max' => 250],
      [['token'], 'string', 'max' => 30],
      [['token'], 'default', 'value' => Yii::$app->security->generateRandomString(10)],
      [['name'], 'unique',  'when' => function ($model, $attribute) {
        return $model->{$attribute} !== $model->getOldAttribute($attribute);
      }],
      [['token'], 'unique'],
      [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['owner_id' => 'id']],
      [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png', 'maxSize' =>  512000, 'tooBig' => 'File larger than expected, limit is 500KB'],
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
      'inviteonly' => 'Invite Only',
      'locked' => 'Locked',
      'recruitment' => 'Recruitment Text'
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
    return $this->hasMany(TeamPlayer::class, ['team_id' => 'id'])->orderBy(['approved' => SORT_DESC, 'ts' => SORT_ASC]);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayers()
  {
    return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('team_player', ['team_id' => 'id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getInvite()
  {
    return $this->hasOne(TeamInvite::class, ['team_id' => 'id']);
  }

  /**
   * Returns the related TeamInvite model.
   *
   * If the invite does not exist yet, it will be created, saved,
   * and populated into the `invite` relation.
   *
   * @return TeamInvite the existing or newly created invite model
   * @throws \RuntimeException if the invite cannot be created
   */
  public function getInviteOrCreate()
  {
    if ($this->invite === null) {
      $invite = new TeamInvite(['team_id' => $this->id, 'token' => Yii::$app->security->generateRandomString(8)]);
      if (!$invite->save()) {
        throw new \RuntimeException('Failed to create TeamInvite');
      }
      $this->populateRelation('invite', $invite);
    }

    return $this->invite;
  }

  public function getValidLogo()
  {
    if ($this->logo === null || trim($this->logo) === '')
      return '../../team_player.png';
    return $this->logo;
  }


  public static function find()
  {
    return new TeamQuery(get_called_class());
  }

  public function createScore($event)
  {
    $ts = new TeamScore();
    $ts->team_id = $this->id;
    $ts->save();
  }
  public function saveLogo()
  {
    if (!$this->uploadedAvatar) {
      return true;
    }

    if (!\app\components\Img::squareAvatar($this->uploadedAvatar->tempName)) {
      return false;
    }

    $fname = Yii::getAlias(sprintf('@app/web/images/avatars/team/%s.png', $this->id));
    $this->updateAttributes(['logo' => $this->id . '.png']);
    return $this->uploadedAvatar->saveAs($fname);
  }

  public function getAcademicShort()
  {
    switch ($this->academic) {
      case 0:
        return 'gov';
      case 1:
        return 'edu';
      default:
        return 'pro';
    }
  }

  public function getAcademicWord()
  {
    switch ($this->academic) {
      case 0:
        return 'government';
      case 1:
        return 'education';
      default:
        return 'professional';
    }
  }
  /**
   * Generate a new invite url
   */
  public function generate_invite()
  {
    if ($this->invite) {
      $this->invite->token = Yii::$app->security->generateRandomString(8);
      if (!$this->invite->save()) {
        throw new UserException(Yii::t('app', 'Failed to save invite. [{error}]', ['error' => implode(" ", $this->invite->getErrors())]));
      }
    } else {
      $ti = new TeamInvite;
      $ti->team_id = $this->id;
      $ti->token = Yii::$app->security->generateRandomString(8);
      if (!$ti->save()) {
        throw new UserException(Yii::t('app', 'Failed to save invite. [{error}]', ['error' => implode(" ", $ti->getErrors())]));
      }
    }
  }
}
