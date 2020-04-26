<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use app\models\PlayerSsl;
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
            [['terms_and_conditions','gdpr'],'boolean'],
            [['terms_and_conditions','gdpr'],'default','value'=>true],
            [['gdpr'], 'in', 'range' => [true],'message'=>'You need to accept our Privacy Policy'],
            [['terms_and_conditions'], 'in', 'range' => [true],'message'=>'You need to accept our Terms and Conditions'],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\Player', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 5, 'max' => 32],
            [['username'], '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin','administrator','echoctf','root','support']],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\Player', 'message' => 'This email address has already been taken.'],

            ['captcha','captcha'],

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
        if (!$this->validate()) {
            return null;
        }

        $player = new Player();
        $player->username = $this->username;
        $player->email = $this->email;
        $player->status=Player::STATUS_INACTIVE;
        $player->setPassword($this->password);
        $player->generateAuthKey();
        $player->generateEmailVerificationToken();
        if($player->save())
        {
          $playerSsl=new PlayerSsl();
          $playerSsl->player_id=$player->id;
          $playerSsl->generate();
          if($playerSsl->save()!==false)
            $playerSsl->refresh();
          $profile=$player->profile;
          $profile->scenario='signup';
          $profile->gdpr=true;
          $profile->terms_and_conditions=true;
          $profile->save();
        }
        return $this->sendEmail($player);

    }
    public function attributeLabels()
    {
        return [
          'terms_and_conditions'=>'I accept the echoCTF RED <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b>',
          'mail_optin'=>'<abbr title="Check this if you would like to receive mail notifications from the platform. We will not use your email address to send you unsolicited emails.">I want to receive emails from echoCTF RED</abbr>',
          'gdpr'=>'I accept the echoCTF RED <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>',
        ];
    }


    /**
     * Sends confirmation email to player
     * @param Player $player player model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($player)
    {
      if(Yii::$app->sys->mail_host!==false)
        \Yii::$app->mailer->transport->setHost(Yii::$app->sys->mail_host);

      if(Yii::$app->sys->mail_port!==false)
        \Yii::$app->mailer->transport->setPort(Yii::$app->sys->mail_port);

      if(Yii::$app->sys->mail_username!==false)
        \Yii::$app->mailer->transport->setUserName(Yii::$app->sys->mail_username);

      if(Yii::$app->sys->mail_password!==false)
        \Yii::$app->mailer->transport->setPassword(Yii::$app->sys->mail_password);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName . ' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Account registration at ' . trim(Yii::$app->sys->event_name))
            ->send();
    }
}
