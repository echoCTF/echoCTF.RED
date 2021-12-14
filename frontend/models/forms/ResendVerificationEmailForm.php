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
                'message' => 'There is no user with this email address.'
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
        if($verification_resend_ip>=5 || $verification_resend_email>=10)
        {
          $this->addError('email', 'Too many resend verficiation email attempts. Please wait and try again.');
          return false;
        }

        if(($player=Player::findOne(['email' => $this->email,'status' => Player::STATUS_INACTIVE]))===null)
        {
            return false;
        }
        $verification_resend_ip++;
        $verification_resend_email++;
        Yii::$app->cache->memcache->set('verification_resend_ip:'.\Yii::$app->request->userIp,$verification_resend_ip, 3600);
        Yii::$app->cache->memcache->set('verification_resend_email:'.$this->email,$verification_resend_email, 3600);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName.' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Account registration for '.trim(Yii::$app->sys->event_name))
            ->send();
    }
}
