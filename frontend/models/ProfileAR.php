<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\game\models\Headshot;
use app\modules\target\models\Target;
use app\modules\target\models\Writeup;

/**
 * This is the model class for table "profile".
 *
 * @property string $id
 * @property int $player_id
 * @property string $visibility
 * @property string $bio
 * @property string $country Player Country
 * @property string $avatar Profile avatar
 * @property string $discord Discord handle
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $youtube Youtube handle
 * @property string $twitch Twitch handle
 * @property string $htb HTB avatar
 * @property boolean $terms_and_conditions
 * @property boolean $mail_optin
 * @property boolean $gdpr
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $visible
 * @property boolean $approved_avatar
 * @property boolean $pending_progress
 * @property string $avtr
 * @property boolean $isMine
 *
 * @property Owner $owner
 * @property Score $score
 * @property Rank $rank
 * @property HeadshotsCount $headshotsCount
 * @property Experience $experience
 * @property TotalTreasures $totalTreasures
*/
class ProfileAR extends \yii\db\ActiveRecord
{
  const SCENARIO_ME='me';
  const SCENARIO_REGISTER='register';
  const SCENARIO_SIGNUP='signup';
  public $gravatar,
          $twitter_avatar,
          $github_avatar;

  public $uploadedAvatar;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'gdpr' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'terms_and_conditions' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'mail_optin' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'approved_avatar' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'pending_progress' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
          ],
        ];
    }
    public function scenarios()
    {
        return [
            'validator' => ['visibility', 'country', 'uploadedAvatar', 'bio','youtube','twitch', 'discord', 'twitter', 'github', 'htb',],
            self::SCENARIO_ME => ['visibility', 'country', 'uploadedAvatar', 'bio','youtube','twitch', 'discord', 'twitter', 'github', 'htb', 'terms_and_conditions', 'mail_optin', 'gdpr','pending_progress'],
            self::SCENARIO_REGISTER => ['username', 'email', 'password'],
            self::SCENARIO_SIGNUP => ['gdpr', 'terms_and_conditions'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim'],
            ['country', 'exist', 'targetClass' => Country::class, 'targetAttribute' => ['country' => 'id']],
            [['player_id', 'country', 'avatar', 'visibility'], 'required'],
            [['terms_and_conditions', 'mail_optin', 'gdpr','approved_avatar','pending_progress'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['approved_avatar'], 'default', 'value'=>Yii::$app->sys->approved_avatar],
            [['visibility'], 'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'], 'default', 'value' =>  Yii::$app->sys->profile_visibility!==false ? Yii::$app->sys->profile_visibility : 'ingame'],
            [['id'], 'default', 'value' =>  new Expression('round(rand()*10000000)'), 'on'=>['register']],
            [['id', 'player_id'], 'integer'],
            [['bio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['avatar'], 'string', 'max' => 255],
            [['country'], 'string', 'max'=>3],
            [['player_id'], 'unique'],
            [['id'], 'unique'],
            [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => 'File larger than expected, limit is 500KB'],

            ['country', 'exist', 'targetClass' => \app\models\Country::class, 'targetAttribute' => ['country' => 'id'],'on'=>'validator'],
            [['avatar'], 'string', 'max' => 255],

            [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim','on'=>'validator'],
            ['twitter', '\app\components\validators\social\TwitterValidator','on'=>'validator'],
            ['twitch', '\app\components\validators\social\TwitchValidator','on'=>'validator'],
            ['github', '\app\components\validators\social\GithubValidator','on'=>'validator'],
            ['youtube', '\app\components\validators\social\YoutubeValidator','on'=>'validator'],
            ['discord', 'filter', 'filter' => [$this, 'normalizeDiscord'],'on'=>'validator'],
            ['discord', '\app\components\validators\social\DiscordValidator','on'=>'validator'],

            ['htb', 'string', 'max' => 8,'on'=>'validator'],
            ['htb', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numberic HTB id is allowed','on'=>'validator'],
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
          'visibility' => 'Profile Visibility',
          'bio' => 'Bio',
          'country' => 'Country',
          'avatar' => 'Avatar',
          'discord' => 'Discord',
          'twitter' => 'Twitter',
          'github' => 'Github',
          'youtube' => 'Youtube',
          'twitch' => 'Twitch',
          'htb'=>'HTB',
          'terms_and_conditions'=>'I accept the '.\Yii::$app->sys->event_name.' <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b>',
          'mail_optin'=>'<abbr title="Check this if you would like to receive mail notifications from the platform. We will not use your email address to send you unsolicited emails.">I want to receive emails from '.\Yii::$app->sys->event_name.'</abbr>',
          'gdpr'=>'I accept the '.\Yii::$app->sys->event_name.' <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>.',
          'created_at' => 'Created At',
          'updated_at' => 'Updated At',
          'owner.username' => 'Username',
          'pending_progress' => 'Pending Progress',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }
    public function getLast()
    {
        return $this->hasOne(PlayerLast::class, ['id' => 'player_id']);
    }
    public function getSpins()
    {
        return $this->hasOne(PlayerSpin::class, ['player_id' => 'player_id'])->todays();
    }
    public function getFullCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country']);
    }

    public function getRank()
    {
        if($this->hasOne(PlayerRank::class, ['player_id' => 'player_id'])->one()!==null)
          return $this->hasOne(PlayerRank::class, ['player_id' => 'player_id']);
        else
          return new PlayerRank;
    }
    public function getCountryRank()
    {
        return $this->hasOne(\app\models\PlayerCountryRank::class, ['player_id' => 'player_id']);
    }

    public function getScore()
    {
        return $this->hasOne(PlayerScore::class, ['player_id' => 'player_id']);
    }
    public function getRCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country']);
    }

    public function getHeadshotRelation()
    {
      return $this->hasMany(Headshot::class, ['player_id' => 'player_id']);
    }

    public function getWriteups()
    {
      return $this->hasMany(Writeup::class, ['player_id' => 'player_id']);
    }

    public function getInvites()
    {
      return $this->hasMany(PlayerRelation::class, ['player_id' => 'player_id']);
    }

    public function getInvitesCount()
    {
      return $this->hasMany(PlayerRelation::class, ['player_id' => 'player_id'])->count();
    }

    public function getHeadshots() {
      return $this->hasMany(Target::class, ['id' => 'target_id'])->via('headshotRelation');
    }

    public function normalizeDiscord($value) {
        while($value!=str_replace('  ',' ',$value))
        {
          $value=str_replace('  ',' ',$value);
        }
        while($value!=str_replace("\xE2\x80\x8B", "", $value))
        {
          $value=str_replace("\xE2\x80\x8B", "", $value);
        }

        return $value;
    }

}
