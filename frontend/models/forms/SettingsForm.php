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
    private $_allowed_fields=[
      'avatar',
      'bio',
      'country',
      'discord',
      'echoctf',
      'email',
      'fullname',
      'github',
      'htb',
      'pending_progress',
      'twitch',
      'twitter',
      'username',
      'visibility',
      'youtube',
    ];
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
    public $echoctf;
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
          [['fullname'], 'trim','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['fullname'], 'string', 'max'=>32,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['pending_progress'], 'boolean', 'trueValue' => true, 'falseValue' => false,'when' => function($model,$attribute) { return $model->_cf($attribute);}],

          /* email field rules */
          [['email'], 'trim','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['email', 'email','checkDNS'=>true,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['email'], 'string', 'max'=>255,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['email'], 'email','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['email', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This email has already been taken.'), 'when' => function($model, $attribute) {
              return $model->_cf($attribute) && $model->{$attribute} !== $model->_player->{$attribute};
          }],
          ['email', 'unique', 'targetClass' => '\app\models\BannedPlayer', 'message' => \Yii::t('app','This email is banned.'), 'when' => function($model, $attribute) {
              return $model->_cf($attribute) && $model->{$attribute} !== $model->_player->{$attribute};
          }],
          ['email', function($attribute, $params){
            $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM banned_player WHERE :email LIKE email')
                ->bindValue(':email', $this->email)
                ->queryScalar();

            if(intval($count)!==0)
                $this->addError($attribute, \Yii::t('app','This email is banned.'));
          }],
          ['email', '\app\components\validators\StopForumSpamValidator', 'max'=>intval(Yii::$app->sys->signup_StopForumSpamValidator),'when' => function($model,$attribute) { return $model->_cf($attribute) && Yii::$app->sys->signup_StopForumSpamValidator!==false;}],
          ['email', '\app\components\validators\MXServersValidator',  'mxonly'=>true, 'when' => function($model,$attribute) { return $model->_cf($attribute) && Yii::$app->sys->signup_MXServersValidator!==false;}],


          /* username field rules */
          [['username'], 'trim','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['username'], 'string', 'min' => intval(Yii::$app->sys->username_length_min), 'max' => intval(Yii::$app->sys->username_length_max),'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['username'], 'match', 'not'=>true, 'pattern'=>'/[^a-zA-Z0-9]/', 'message'=>\Yii::t('app','Invalid characters in username.'),'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['username'], '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'administrator', 'echoctf', 'root', 'support'],'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['username'], 'required', 'message' => \Yii::t('app','Please choose a username.'),'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['username', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This username has already been taken.'), 'when' => function($model, $attribute) {
              return $model->_cf($attribute) && $model->{$attribute} !== $model->_player->{$attribute};
          }],
          [['new_password', ], 'string', 'max'=>255],
          [['confirm_password'], 'string', 'max'=>255],
          [['new_password'], 'compare', 'compareAttribute'=>'confirm_password'],
          [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['country', 'exist', 'targetClass' => \app\models\Country::class, 'targetAttribute' => ['country' => 'id'],'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['avatar','youtube','twitch'], 'string', 'max' => 255,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['discord', 'filter', 'filter' => [$this, 'normalizeDiscord'],'when' => function($model,$attribute) { return $model->_cf($attribute);}],

          ['twitter', '\app\components\validators\social\TwitterValidator','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['twitch', '\app\components\validators\social\TwitchValidator','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['github', '\app\components\validators\social\GithubValidator','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['youtube', '\app\components\validators\social\YoutubeValidator','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['discord', '\app\components\validators\social\DiscordValidator','when' => function($model,$attribute) { return $model->_cf($attribute);}],

          ['echoctf', 'string', 'max' => 8,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['echoctf', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numbers are allowed for echoCTF.RED profile','when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['htb', 'string', 'max' => 8,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          ['htb', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numbers are allowed for HTB profile','when' => function($model,$attribute) { return $model->_cf($attribute);}],

          [['country'], 'string', 'max'=>3,'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => \Yii::t('app','File larger than expected, limit is {sizeLimit}',['sizeLimit'=>'500KB'])],
          [['visibility'], 'in', 'range' => ['public', 'private', 'ingame'],'when' => function($model,$attribute) { return $model->_cf($attribute);}],
          [['visibility'], 'default', 'value' =>  Yii::$app->sys->profile_visibility!==false ? Yii::$app->sys->profile_visibility : 'ingame','when' => function($model,$attribute) { return $model->_cf($attribute);}],
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
    public function setAllowed_fields($value)
    {
      asort($value);
      $this->_allowed_fields=$value;
    }

    public function getAllowed_fields()
    {
      return $this->_allowed_fields;
    }

    public function _cf($attribute):bool
    {
      return array_search($attribute,$this->allowed_fields,true)!==false;
    }

    public function init()
    {
      if(Yii::$app->sys->profile_settings_fields!==false && trim(Yii::$app->sys->profile_settings_fields)!="")
        $this->allowed_fields=explode(",",Yii::$app->sys->profile_settings_fields);
      $this->_player=Yii::$app->user->identity;
      if($this->_cf('username')) $this->username=$this->_player->username;
      if($this->_cf('fullname')) $this->fullname=$this->_player->fullname;
      if($this->_cf('email')) $this->email=$this->_player->email;
      if($this->_cf('email')) $this->visibility=$this->_player->profile->visibility;
      if($this->_cf('country')) $this->country=$this->_player->profile->country;
      if($this->_cf('avatar')) $this->avatar=$this->_player->profile->avatar;
      if($this->_cf('pending_progress')) $this->pending_progress=$this->_player->profile->pending_progress;
      if($this->_cf('bio')) $this->bio=$this->_player->profile->bio;
      $this->_player->profile->scenario='validator';

      if($this->_cf('discord') && $this->_player->profile->validate('discord'))
        $this->discord=$this->_player->profile->discord;

      if($this->_cf('twitter') && $this->_player->profile->validate('twitter'))
        $this->twitter=$this->_player->profile->twitter;

      if($this->_cf('youtube') && $this->_player->profile->validate('youtube'))
        $this->youtube=$this->_player->profile->youtube;

      if($this->_cf('twitch') && $this->_player->profile->validate('twitch'))
        $this->twitch=$this->_player->profile->twitch;

      if($this->_cf('github') && $this->_player->profile->validate('github'))
        $this->github=$this->_player->profile->github;

      if($this->_cf('htb') && $this->_player->profile->validate('htb'))
        $this->htb=$this->_player->profile->htb;

      if($this->_cf('echoctf') && $this->_player->profile->validate('echoctf'))
        $this->echoctf=$this->_player->profile->echoctf;

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
      $emailtpl=\app\modelscli\EmailTemplate::findOne(['name' => 'emailChangeVerify']);
      $subject=\Yii::t('app','Verify your email for {event_name}',['event_name'=>trim(Yii::$app->sys->event_name)]);
      $verifyLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $this->_player->verification_token]);
      $contentHtml = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->html, ['user' => $this->_player,'verifyLink'=>$verifyLink]);
      $contentTxt = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->txt, ['user' => $this->_player,'verifyLink'=>$verifyLink]);

      return $this->_player->mail($subject,$contentHtml,$contentTxt);
    }

    public function save()
    {
      if($this->_cf('username')) $this->_player->username=$this->username;
      if($this->_cf('fullname')) $this->_player->fullname=$this->fullname;
      if($this->_cf('email') && $this->email!=$this->_player->email)
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
      if($this->_cf('visibility')) $this->_player->profile->visibility=$this->visibility;
      if($this->_cf('pending_progress')) $this->_player->profile->pending_progress=$this->pending_progress;
      if($this->_cf('country')) $this->_player->profile->country=$this->country;
      if($this->_cf('avatar')) $this->_player->profile->avatar=$this->avatar;
      if($this->_cf('bio')) $this->_player->profile->bio=$this->bio;
      if($this->_cf('discord')) $this->_player->profile->discord=$this->discord;
      if($this->_cf('twitter')) $this->_player->profile->twitter=$this->twitter;
      if($this->_cf('youtube')) $this->_player->profile->youtube=$this->youtube;
      if($this->_cf('twitch')) $this->_player->profile->twitch=$this->twitch;
      if($this->_cf('github')) $this->_player->profile->github=$this->github;
      if($this->_cf('htb')) $this->_player->profile->htb=$this->htb;
      if($this->_cf('echoctf')) $this->_player->profile->echoctf=$this->echoctf;
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
