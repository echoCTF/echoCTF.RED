<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use app\models\PlayerSsl;
use app\models\PlayerRelation;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $terms_and_conditions;
    public $gdpr;
    public $captcha;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['terms_and_conditions','gdpr'],'required'],
            [['terms_and_conditions', 'gdpr'], 'boolean'],
            [['terms_and_conditions', 'gdpr'], 'default', 'value'=>true],
            [['gdpr'], 'in', 'range' => [true], 'message'=>\Yii::t('app','You need to accept our Privacy Policy')],
            [['terms_and_conditions'], 'in', 'range' => [true], 'message'=>\Yii::t('app','You need to accept our Terms and Conditions')],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This username has already been taken.')],
            ['username', 'string', 'min' => intval(Yii::$app->sys->username_length_min), 'max' => intval(Yii::$app->sys->username_length_max)],
            [['username'], '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'administrator', 'echoctf', 'root', 'support']],
            [['username'], 'match', 'not'=>true, 'pattern'=>'/[^a-zA-Z0-9]/', 'message'=>\Yii::t('app','Invalid characters in username.')],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email','checkDNS'=>true],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This email address has already been taken.')],
            ['email', 'unique', 'targetClass' => '\app\models\BannedPlayer', 'message' => \Yii::t('app','This email is banned.')],
            ['email', function($attribute, $params){
              $count = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM banned_player WHERE :email LIKE email')
                  ->bindValue(':email', $this->email)
                  ->queryScalar();

              if(intval($count)!==0)
                  $this->addError($attribute, \Yii::t('app','This email is banned.'));
            }],
            ['username', '\app\components\validators\HourRegistrationValidator',    'client_ip'=>\Yii::$app->request->userIp, 'max'=>Yii::$app->sys->signup_HourRegistrationValidator,'when' => function($model) { return Yii::$app->sys->signup_HourRegistrationValidator!==false;}],
            ['username', '\app\components\validators\TotalRegistrationsValidator',  'client_ip'=>\Yii::$app->request->userIp, 'max'=>Yii::$app->sys->signup_TotalRegistrationsValidator,'when' => function($model) { return Yii::$app->sys->signup_TotalRegistrationsValidator!==false;}],
            ['email',    '\app\components\validators\VerifymailValidator',          'when' => function($model) { return (bool)Yii::$app->sys->signup_ValidatemailValidator;}],
            ['email',    '\app\components\validators\StopForumSpamValidator',       'max'=>Yii::$app->sys->signup_StopForumSpamValidator,'when' => function($model) { return Yii::$app->sys->signup_StopForumSpamValidator!==false;}],
            ['email',   '\app\components\validators\MXServersValidator',  'mxonly'=>true,           'when' => function($model) { return Yii::$app->sys->signup_MXServersValidator!==false;}],
            //['email', '\app\components\validators\WhoisValidator', ],

            ['captcha', 'captcha'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs player up.
     *
     * @return Player The model of the new account
     */
    public function signup()
    {
        if(!$this->validate())
        {
          throw new \Exception(\Yii::t('app',"Error Processing Request"), 1);
        }

        $player=new Player();
        $player->username=$this->username;
        $player->email=$this->email;
        $player->setPassword($this->password);
        $player->generateAuthKey();

        if(\Yii::$app->sys->require_activation===true)
        {
          $player->active=0;
          $player->generateEmailVerificationToken();
          if($player->saveNewPlayer()===false)
          {
            throw new \Exception(\Yii::t('app',"Error Processing Request"), 1);
          }
        }
        else
        {
          $player->active=1;
          if($player->saveWithSsl()===false)
          {
            throw new \Exception(\Yii::t('app',"Error Processing Request"), 1);
          }
          $player->genAvatar();
          $player->trigger(Player::NEW_PLAYER);
        }

        $player->profile->last->signup_ip=ip2long(\Yii::$app->request->userIp);
        $player->profile->last->save();

        if(Yii::$app->getSession()->get('referred_by')!==null)
        {
          $pr=new PlayerRelation;
          $pr->player_id=Yii::$app->getSession()->get('referred_by');
          $pr->referred_id=$player->id;
          $pr->save();
        }

        $counter=intval(\Yii::$app->cache->memcache->get('registeredip:'.\Yii::$app->request->userIp));
        Yii::$app->cache->memcache->set('registeredip:'.\Yii::$app->request->userIp,$counter+1,3600);
        if(Yii::$app->sys->require_activation===true && !$this->sendEmail($player))
        {
          throw new \Exception(\Yii::t('app',"Error Processing Request. Failed to mail verification email!"), 1);
        }

        return $player;
    }

    public function attributeLabels()
    {
      return [
        'terms_and_conditions'=>\Yii::t('app','I accept the {event_name} <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b>',['event_name'=>\Yii::$app->sys->event_name]),
        'mail_optin'=>\Yii::t('app','<abbr title="Check this if you would like to receive mail notifications from the platform. We will not use your email address to send you unsolicited emails.">I want to receive emails from {event_name}</abbr>',['event_name'=>\Yii::$app->sys->event_name]),
        'gdpr'=>\Yii::t('app','I accept the {event_name} <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>',['event_name'=>\Yii::$app->sys->event_name]),
      ];
    }


    /**
     * Sends confirmation email to player
     * @param Player $player player model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($player)
    {
      $emailtpl=\app\modelscli\EmailTemplate::findOne(['name' => 'emailVerify']);
      $subject=\Yii::t('app','Account registration for {event_name}', ['event_name'=>trim(Yii::$app->sys->event_name)]);
      $verifyLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $player->verification_token]);

      $contentHtml = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->html, ['user' => $player,'verifyLink'=>$verifyLink]);
      $contentTxt = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->txt, ['user' => $player,'verifyLink'=>$verifyLink]);
      return $player->mail($subject,$contentHtml,$contentTxt);
    }
}
