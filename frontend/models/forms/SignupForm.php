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
            ['username', 'string', 'min' => 5, 'max' => 32],
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
            ['username', '\app\components\validators\HourRegistrationValidator', 'client_ip'=>\Yii::$app->request->userIp],
            ['username', '\app\components\validators\TotalRegistrationsValidator', 'client_ip'=>\Yii::$app->request->userIp],
            ['email', '\app\components\validators\StopForumSpamValidator', ],
            //['email', '\app\components\validators\WhoisValidator', ],
            ['email', '\app\components\validators\MXServersValidator', ],

            ['captcha', 'captcha'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs player up.
     *
     * @return bool whether the creating new account was successful and email was sent
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
        if(\Yii::$app->sys->require_activation===true)
        {
          $player->active=0;
          $player->generateEmailVerificationToken();
        }
        else
        {
          $player->active=1;
        }

        $player->setPassword($this->password);
        $player->generateAuthKey();
        if($player->saveNewPlayer()===false)
        {
          throw new \Exception(\Yii::t('app',"Error Processing Request"), 1);
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
        $counter=intval(\Yii::$app->sys->{'registeredip:'.\Yii::$app->request->userIp});
        Yii::$app->cache->set('registeredip:'.\Yii::$app->request->userIp,$counter+1,3600);
        if(Yii::$app->sys->require_activation===true)
          return $this->sendEmail($player);
        return true;

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
      return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName.' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Account registration at '.trim(Yii::$app->sys->event_name))
            ->send();
    }
}
