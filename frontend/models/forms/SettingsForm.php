<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Player|null $player The player model we will update after validation
 * @property string $new_password The new password provided by the user
 * @property string $confirm_password The confirm password provided by the user
 * @property string $fullname The fullname provided by the user
 * @property string $email The new email provided by the user
 * @property string $username The new email provided by the user
 * @property string $visibility Profile visibility
 * @property string $bio The profile bio for the user
 * @property string $country Player Country
 * @property string $avatar Profile avatar
 * @property string $discord Discord handle
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $youtube Youtube handle
 * @property string $twitch Twitch handle
 * @property string $htb HTB avatar
 *
 */
class SettingsForm extends Model
{
    private $_player=null;
    public $new_password;
    public $confirm_password;
    public $fullname;
    public $email;
    public $username;
    public $visibilities=[
        'private'=>'Private',
        'public'=>'Public',
        'ingame'=>'In Game',
      ];
    public $bio;
    public $htb;
    public $avatar;
    public $github;
    public $twitch;
    public $twitter;
    public $country;
    public $discord;
    public $youtube;
    public $visibility;
    public $pending_progress;
    public $uploadedAvatar;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
          [['fullname'], 'trim'],
          [['fullname'], 'string', 'max'=>32],
          [['pending_progress'], 'boolean', 'trueValue' => true, 'falseValue' => false],

          /* email field rules */
          [['email'], 'trim'],
          [['email'], 'string', 'max'=>255],
          [['email'], 'email'],
          ['email', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This email has already been taken.'), 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->_player->{$attribute};
          }],
          ['email', 'unique', 'targetClass' => '\app\models\BannedPlayer', 'message' => \Yii::t('app','This email is banned.'), 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->_player->{$attribute};
          }],
          ['email', function($attribute, $params){
            $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM banned_player WHERE :email LIKE email')
                ->bindValue(':email', $this->email)
                ->queryScalar();

            if(intval($count)!==0)
                $this->addError($attribute, \Yii::t('app','This email is banned.'));
          }],
          ['email', '\app\components\validators\StopForumSpamValidator', 'max'=>Yii::$app->sys->signup_StopForumSpamValidator,'when' => function($model) { return Yii::$app->sys->signup_StopForumSpamValidator!==false;}],
          ['email', '\app\components\validators\MXServersValidator', 'when' => function($model) { return Yii::$app->sys->signup_MXServersValidator!==false;}],


          /* username field rules */
          [['username'], 'trim'],
          [['username'], 'string', 'min' => intval(Yii::$app->sys->username_length_min), 'max' => intval(Yii::$app->sys->username_length_max)],
          [['username'], 'match', 'not'=>true, 'pattern'=>'/[^a-zA-Z0-9]/', 'message'=>\Yii::t('app','Invalid characters in username.')],
          [['username'], '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'administrator', 'echoctf', 'root', 'support']],
          [['username'], 'required', 'message' => \Yii::t('app','Please choose a username.')],
          ['username', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This username has already been taken.'), 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->_player->{$attribute};
          }],
          [['new_password', ], 'string', 'max'=>255],
          [['confirm_password'], 'string', 'max'=>255],
          [['new_password'], 'compare', 'compareAttribute'=>'confirm_password'],
          [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim'],
          ['country', 'exist', 'targetClass' => \app\models\Country::class, 'targetAttribute' => ['country' => 'id']],
          [['avatar','youtube','twitch'], 'string', 'max' => 255],
          ['discord', 'filter', 'filter' => [$this, 'normalizeDiscord']],

          ['twitter', '\app\components\validators\social\TwitterValidator'],
          ['twitch', '\app\components\validators\social\TwitchValidator'],
          ['github', '\app\components\validators\social\GithubValidator'],
          ['youtube', '\app\components\validators\social\YoutubeValidator'],
          ['discord', '\app\components\validators\social\DiscordValidator'],

          ['htb', 'string', 'max' => 8],
          ['htb', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numberic HTB id is allowed'],

          [['country'], 'string', 'max'=>3],
          [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => \Yii::t('app','File larger than expected, limit is {sizeLimit}',['sizeLimit'=>'500KB'])],
          [['visibility'], 'in', 'range' => ['public', 'private', 'ingame']],
          [['visibility'], 'default', 'value' =>  Yii::$app->sys->profile_visibility!==false ? Yii::$app->sys->profile_visibility : 'ingame'],
        ];
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
    public function init()
    {
      $this->_player=Yii::$app->user->identity;
      $this->username=$this->_player->username;
      $this->fullname=$this->_player->fullname;
      $this->email=$this->_player->email;
      $this->visibility=$this->_player->profile->visibility;
      $this->country=$this->_player->profile->country;
      $this->avatar=$this->_player->profile->avatar;
      $this->pending_progress=$this->_player->profile->pending_progress;
      $this->bio=$this->_player->profile->bio;
      $this->_player->profile->scenario='validator';

      if($this->_player->profile->validate('discord'))
        $this->discord=$this->_player->profile->discord;

      if($this->_player->profile->validate('twitter'))
        $this->twitter=$this->_player->profile->twitter;

      if($this->_player->profile->validate('youtube'))
        $this->youtube=$this->_player->profile->youtube;

      if($this->_player->profile->validate('twitch'))
        $this->twitch=$this->_player->profile->twitch;

      if($this->_player->profile->validate('github'))
        $this->github=$this->_player->profile->github;

      if($this->_player->profile->validate('htb'))
        $this->htb=$this->_player->profile->htb;

      parent::init();
    }
    /**
     * Finds player by [[username]]|[[email]]
     *
     * @return Player|null
     */
    public function getPlayer()
    {
      return $this->_player;
    }

    public function setPlayer($value)
    {
      $this->_player=$value;
    }

    /**
     * Sends email verification link to player after successful email change
     * @param Player $player player model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail()
    {
      return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailChangeVerify-html', 'text' => 'emailChangeVerify-text'],
                ['user' => $this->player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName.' robot'])
            ->setTo([$this->player->email => $this->player->fullname])
            ->setSubject(\Yii::t('app','Verify your email for {event_name}',['event_name'=>trim(Yii::$app->sys->event_name)]))
            ->send();
    }

    public function save()
    {
      $this->_player->username=$this->username;
      $this->_player->fullname=$this->fullname;
      if($this->email!=$this->_player->email)
      {
        $this->_player->email=$this->email;
        $this->_player->generateEmailVerificationToken();
        $this->_player->status=Player::STATUS_UNVERIFIED;
        $this->sendEmail();
        \Yii::$app->session->addFlash('info',\Yii::t('app','You will receive an email to verify your new email address.'));
      }

      if($this->new_password)
      {
        $this->_player->password=\Yii::$app->security->generatePasswordHash($this->new_password);
        \Yii::$app->session->addFlash('success',\Yii::t('app','Password changed.'));
      }

      $this->_player->profile->scenario="me";
      $this->_player->profile->visibility=$this->visibility;
      $this->_player->profile->pending_progress=$this->pending_progress;
      $this->_player->profile->country=$this->country;
      $this->_player->profile->avatar=$this->avatar;
      $this->_player->profile->bio=$this->bio;
      $this->_player->profile->discord=$this->discord;
      $this->_player->profile->twitter=$this->twitter;
      $this->_player->profile->youtube=$this->youtube;
      $this->_player->profile->twitch=$this->twitch;
      $this->_player->profile->github=$this->github;
      $this->_player->profile->htb=$this->htb;
      if($this->_player->save() && $this->_player->profile->save())
      {
        \Yii::$app->session->addFlash('success',\Yii::t('app','Profile update successful.'));
      }

    }
    public function reset()
    {
      $this->player->refresh();
      $this->new_password=$this->confirm_password=null;
      $this->uploadedAvatar=null;
      $this->init();
    }

}
