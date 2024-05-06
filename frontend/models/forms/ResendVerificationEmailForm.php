<?php


namespace app\models\forms;

use Yii;
use app\models\Player;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;

class ResendVerificationEmailForm extends Model
{
    /**
     * @var string
     */
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\Player',
                'filter' => ['status' => Player::STATUS_INACTIVE],
                'message' => \Yii::t('app','There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail()
    {
        $verification_resend_ip=intval(Yii::$app->cache->memcache->get('verification_resend_ip:'.\Yii::$app->request->userIp));
        $verification_resend_email=intval(Yii::$app->cache->memcache->get('verification_resend_usename:'.$this->email));
        if((Yii::$app->sys->verification_resend_ip!==false && $verification_resend_ip>=intval(\Yii::$app->sys->verification_resend_ip)) || (Yii::$app->sys->verification_resend_email!==false && $verification_resend_email>=Yii::$app->sys->verification_resend_email))
        {
          $this->addError('email', \Yii::t('app','Too many resend verification attempts. Please wait and try again.'));
          return false;
        }

        if(($player=Player::findOne(['email' => $this->email,'status' => Player::STATUS_INACTIVE]))===null)
        {
            return false;
        }
        $verification_resend_ip++;
        $verification_resend_email++;
        Yii::$app->cache->memcache->set('verification_resend_ip:'.\Yii::$app->request->userIp,$verification_resend_ip, Yii::$app->sys->verification_resend_ip_timeout);
        Yii::$app->cache->memcache->set('verification_resend_email:'.$this->email,$verification_resend_email, Yii::$app->sys->verification_resend_email_timeout);
        $emailtpl=\app\modelscli\EmailTemplate::findOne(['name' => 'emailVerify']);
        $subject=\Yii::t('app','Account registration for {event_name}', ['event_name'=>trim(Yii::$app->sys->event_name)]);
        $verifyLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $player->verification_token]);
        $contentHtml = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->html, ['user' => $player,'verifyLink'=>$verifyLink]);
        $contentTxt = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->txt, ['user' => $player,'verifyLink'=>$verifyLink]);
        return $player->mail($subject,$contentHtml,$contentTxt);
    }
}
