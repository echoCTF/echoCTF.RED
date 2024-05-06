<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
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
                'filter' => ['status' => Player::STATUS_ACTIVE],
                'message' => \Yii::t('app','There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $player Player */
        $player=Player::findOne([
            'status' => Player::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        $password_reset_ip=intval(Yii::$app->cache->memcache->get('password_reset_ip:'.\Yii::$app->request->userIp));
        $password_reset_email=intval(Yii::$app->cache->memcache->get('password_reset_email:'.$this->email));
        if((Yii::$app->sys->password_reset_ip!==false && $password_reset_ip>=intval(Yii::$app->sys->password_reset_ip)) || (Yii::$app->sys->password_reset_email!==false && $password_reset_email>=intval(Yii::$app->sys->password_reset_email)))
        {
          $this->addError('email', \Yii::t('app','Too many password reset requests. Please wait and try again later.'));
          return false;
        }

        if($player === null)
        {
            return false;
        }

        if(!Player::isPasswordResetTokenValid($player->password_reset_token))
        {
            $player->generatePasswordResetToken();
            if(!$player->save())
            {
                return false;
            }
        }
        $password_reset_ip++;
        $password_reset_email++;
        Yii::$app->cache->memcache->set('password_reset_ip:'.\Yii::$app->request->userIp,$password_reset_ip, \Yii::$app->sys->password_reset_ip_timeout);
        Yii::$app->cache->memcache->set('password_reset_email:'.$this->email,$password_reset_email, \Yii::$app->sys->password_reset_email_timeout);
        $emailtpl=\app\modelscli\EmailTemplate::findOne(['name' => 'passwordResetToken']);
        $subject=\Yii::t('app','Password reset request for {event_name}',['event_name'=>trim(Yii::$app->sys->event_name)]);
        $resetLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $player->password_reset_token]);
        $contentHtml = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->html, ['user' => $player,'resetLink'=>$resetLink]);
        $contentTxt = \app\components\echoCTFView::renderPhpContent("?>" . $emailtpl->txt, ['user' => $player,'resetLink'=>$resetLink]);
        $mailHeaders=[
          ['X-tag','password-reset'],
          ['X-Metadata-Requestor-IP',\Yii::$app->request->userIp],
        ];
        return $player->mail($subject,$contentHtml,$contentTxt,$mailHeaders);
    }
}
