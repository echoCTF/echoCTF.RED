<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\game\models\Headshot;
use app\modules\target\models\Target;

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
 * @property string $htb HTB avatar
 * @property boolean $terms_and_conditions
 * @property boolean $mail_optin
 * @property boolean $gdpr
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $visible
 *
 * @property Owner $owner
 * @property Score $score
 * @property Rank $rank
 * @property HeadshotsCount $headshotsCount
 * @property Experience $experience
 * @property TotalTreasures $totalTreasures
*/
class Profile extends \yii\db\ActiveRecord
{
  const SCENARIO_ME='me';
  const SCENARIO_REGISTER='register';
  const SCENARIO_SIGNUP='signup';
  public $gravatar,
          $twitter_avatar,
          $github_avatar;
  public $visibilities=[
      'private'=>'Private',
      'public'=>'Public',
      'ingame'=>'In Game',
    ];
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
            self::SCENARIO_ME => ['visibility', 'country', 'bio', 'discord', 'twitter', 'github', 'htb', 'terms_and_conditions', 'mail_optin', 'gdpr'],
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
            [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio'], 'trim'],
            ['country', 'exist', 'targetClass' => Country::class, 'targetAttribute' => ['country' => 'id']],
//            ['avatar', 'exist', 'targetClass' => Avatar::class, 'targetAttribute' => ['avatar' => 'id']],
            [['player_id', 'country', 'avatar', 'visibility'], 'required'],
            [['terms_and_conditions', 'mail_optin', 'gdpr'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['visibility'], 'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'], 'default', 'value' =>  'ingame'],
            [['id'], 'default', 'value' =>  new Expression('round(rand()*10000000)'), 'on'=>['register']],
            [['id', 'player_id'], 'integer'],
            [['bio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['avatar','twitter', 'github'], 'string', 'max' => 255],
            [['country'], 'string', 'max'=>3],
            [['player_id'], 'unique'],
            [['id'], 'unique'],
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
          'htb'=>'HTB',
          'terms_and_conditions'=>'I accept the echoCTF RED <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b>',
          'mail_optin'=>'<abbr title="Check this if you would like to receive mail notifications from the platform. We will not use your email address to send you unsolicited emails.">I want to receive emails from echoCTF RED</abbr>',
          'gdpr'=>'I accept the echoCTF RED <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>.',
          'created_at' => 'Created At',
          'updated_at' => 'Updated At',
          'owner.username' => 'Username',
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

    public function getRank()
    {
        return $this->hasOne(PlayerRank::class, ['player_id' => 'player_id']);
    }
    public function getScore()
    {
        return $this->hasOne(PlayerScore::class, ['player_id' => 'player_id']);
    }
    public function getRCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country']);
    }
    public function getVisible(): bool
    {
      if(Yii::$app->sys->player_profile === false) return false;
      elseif($this->visibility == 'public') return true;
      elseif(intval(Yii::$app->user->id) === intval($this->player_id)) return true;
      elseif(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin !== false) return true;
      else return array_search($this->visibility, ['public', 'ingame'], true) === FALSE ? false : true;
    }

    /**
     * Get profile link based on player permissions,
     */
    public function getLink()
    {

      if(intval(Yii::$app->user->id) === intval($this->player_id))
        return Html::a(Html::encode($this->owner->username), ['/profile/me']);
      else if($this->visible === true)
        return Html::a(Html::encode($this->owner->username), ['/profile/index', 'id'=>$this->id], ['data-pjax'=>0]);
      return Html::encode($this->owner->username);
    }

    public function getLinkto()
    {

      if(intval(Yii::$app->user->id) === intval($this->player_id)) return Url::to(['/profile/me']);
      else if($this->visible === true) return Url::to(['/profile/index', 'id'=>$this->id]);
      return null;
    }

    public function getExperience()
    {
      //return $this->hasOne(Experience::class, ['id' => 'player_id']);
      return Experience::find()->where("{$this->score->points} BETWEEN min_points AND max_points");
    }
    public function getTotalTreasures()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_treasure WHERE player_id=:player_id')->bindValue(':player_id', $this->player_id)->queryScalar();
    }
    public function getTotalFindings()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_finding WHERE player_id=:player_id')->bindValue(':player_id', $this->player_id)->queryScalar();
    }
    public function getHeadshotRelation()
    {
      return $this->hasMany(Headshot::class, ['player_id' => 'player_id']);
    }
    public function getHeadshots() {
      return $this->hasMany(Target::class, ['id' => 'target_id'])->via('headshotRelation');
    }

    public function getHeadshotsCount():int {
      return (int) $this->hasMany(Headshot::class, ['player_id' => 'player_id'])->count();
    }

    public function getIsMine():bool
    {
      if(Yii::$app->user->isGuest)
        return false;
      if(Yii::$app->user->id === $this->player_id)
        return true;
      return false;
    }
    public function getTwitterHandle()
    {
      if($this->twitter != "")
      {
        return $this->twitter{0} === '@' ? $this->twitter : '@'.$this->twitter;
      }
      return $this->owner->username;
    }
    public function getBraggingRights()
    {
      if($this->rank)
      {
        $msg=sprintf("I am at the %s place with %d pts", $this->rank->ordinalPlace, $this->score->points);
        if($this->headshotsCount > 0)
        {
          $msg.=sprintf(', and %d headshots', $this->headshotsCount);
        }
      }
      else
        $msg=sprintf("I have just joined echoCTF.RED!");
      return $msg;
    }
}
