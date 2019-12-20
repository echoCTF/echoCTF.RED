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
 * @property string $discord Profile avatar
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $htb HTB avatar
 * @property int $terms_and_conditions
 * @property int $mail_optin
 * @property int $gdpr
 * @property string $created_at
 * @property string $updated_at
 */
class Profile extends \yii\db\ActiveRecord
{
  const SCENARIO_ME = 'me';
  const SCENARIO_REGISTER = 'register';
  const SCENARIO_SIGNUP = 'signup';
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
                'class' => AttributeTypecastBehavior::className(),
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
            self::SCENARIO_ME => ['visibility','country','avatar','bio','discord','twitter','github','htb','terms_and_conditions','mail_optin','gdpr'],
            self::SCENARIO_REGISTER => ['username', 'email', 'password'],
            self::SCENARIO_SIGNUP => ['gdpr','terms_and_conditions'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discord','twitter','github','htb','avatar','bio'],'trim'],
            ['country', 'exist', 'targetClass' => Country::class, 'targetAttribute' => ['country' => 'id']],
            ['avatar', 'exist', 'targetClass' => Avatar::class, 'targetAttribute' => ['avatar' => 'id']],
            [['player_id','country','avatar','visibility'], 'required'],
            [['terms_and_conditions','mail_optin','gdpr'],'boolean', 'trueValue' => true, 'falseValue' => false],
            [['visibility'],'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'],'default', 'value' =>  'ingame'],
            [['id'],'default', 'value' =>  new Expression('round(rand()*10000000)'),'on'=>['register']],
            [['id', 'player_id'], 'integer'],
            [['bio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['avatar', 'twitter','github'], 'string', 'max' => 255],
            [['country'], 'string','max'=>3],
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
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }
    public function getLast()
    {
        return $this->hasOne(PlayerLast::className(), ['id' => 'player_id']);
    }
    public function getSpins()
    {
        return $this->hasOne(PlayerSpin::className(), ['player_id' => 'player_id'])->todays();
    }

    public function getRank()
    {
        return $this->hasOne(PlayerRank::className(), ['player_id' => 'player_id']);
    }
    public function getScore()
    {
        return $this->hasOne(PlayerScore::className(), ['player_id' => 'player_id']);
    }
    public function getRCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country']);
    }
    public function getVisible(): bool
  	{
  		if(Yii::$app->sys->player_profile===false) return false;
  		elseif($this->visibility=='public') return true;
  		elseif(intval(Yii::$app->user->id)===intval($this->player_id)) return true;
      elseif(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) return true;
  		else return array_search($this->visibility,['public','ingame'],true) === FALSE ? false : true;
  	}

    /**
     * Get profile link based on player permissions,
     */
    public function getLink()
  	{

  		if(intval(Yii::$app->user->id)===intval($this->player_id))
        return Html::a(Html::encode($this->owner->username),['/profile/me']);
      else if($this->visible===true)
        return Html::a(Html::encode($this->owner->username),['/profile/index','id'=>$this->id],['data-pjax'=>0]);
  		return Html::encode($this->owner->username);
  	}

    public function getLinkto()
  	{

  		if(intval(Yii::$app->user->id)===intval($this->player_id)) return Url::to(['/profile/me']);
  		else if($this->visible===true) return Url::to(['/profile/index','id'=>$this->id]);
  		return null;
  	}

    public function getExperience()
		{
      //return $this->hasOne(Experience::className(), ['id' => 'player_id']);
			return Experience::find()->where("{$this->score->points} BETWEEN min_points AND max_points");
		}
    public function getTotalTreasures()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_treasure WHERE player_id=:player_id')->bindValue(':player_id',$this->player_id)->queryScalar();
    }
    public function getTotalFindings()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_finding WHERE player_id=:player_id')->bindValue(':player_id',$this->player_id)->queryScalar();
    }
    public function getHeadshotRelation()
    {
      return $this->hasMany(Headshot::className(), ['player_id' => 'player_id']);
    }
    public function getHeadshots(){
      return $this->hasMany(Target::className(),['id' => 'target_id'])->via('headshotRelation');
    }

    public function getHeadshotsCount():int {
      return (int)$this->hasMany(Headshot::className(), ['player_id' => 'player_id'])->count();
    }

    public function getIsMine():bool
    {
      if(Yii::$app->user->isGuest)
        return false;
      if(Yii::$app->user->id===$this->player_id)
        return true;
      return false;
    }
    public function getTwitterHandle()
    {
      if($this->twitter!="")
        return '@'.$this->twitter;
      return $this->owner->username;
    }

}
