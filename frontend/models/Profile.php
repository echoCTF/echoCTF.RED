<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Html;

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


    public function scenarios()
    {
        return [
            self::SCENARIO_ME => ['visibility','country','avatar','bio','discord','twitter','github','htb','terms_and_conditions','mail_optin','gdpr'],
            self::SCENARIO_REGISTER => ['username', 'email', 'password'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['terms_and_conditions','mail_optin','gdpr'],'boolean', 'trueValue' => true, 'falseValue' => false],
            [['visibility'],'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'],'default', 'value' =>  'ingame'],
            [['id'],'default', 'value' =>  new Expression('round(rand()*10000000)')],
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
    public function getVisible()
  	{
  		if(Yii::$app->sys->player_profile===false) return false;
  		elseif(Yii::$app->user->isGuest && $this->visibility=='public') return true;
  		elseif(intval(Yii::$app->user->id)===intval($this->player_id)) return true;
  		else return array_search($this->visibility,['public','ingame'],true) === FALSE ? false : true;
  	}

    /**
     * Get profile link based on player permissions,
     */
    public function getLink()
  	{

  		if(intval(Yii::$app->user->id)===intval($this->player_id)) return Html::a(Html::encode($this->owner->username),['/profile/me']);
  		else if($this->visible===true) return Html::a(Html::encode($this->owner->username),['/profile/index','id'=>$this->id],['data-pjax'=>0]);
  		return Html::encode($this->owner->username);
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
    public function getHeadshots(){
      $command = Yii::$app->db->createCommand('select t.* FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id)');
      $command->bindValue(':player_id',$this->player_id);
      $headshots = $command->query()->readAll();
      return $headshots;
    }
    public function getHeadshotsCount(){
      $command = Yii::$app->db->createCommand('select count(*) FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id)');
      $command->bindValue(':player_id',$this->player_id);
      $headshots = $command->queryScalar();
      return (int)$headshots;
    }

}
