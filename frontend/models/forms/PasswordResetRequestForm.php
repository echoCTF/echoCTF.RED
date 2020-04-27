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
                'message' => 'There is no user with this email address.'
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
        $player = Player::findOne([
            'status' => Player::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        if ($player===null) {
            return false;
        }

        if (!Player::isPasswordResetTokenValid($player->password_reset_token)) {
            $player->generatePasswordResetToken();
            if (!$player->save()) {
                return false;
            }
        }
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
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName . ' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Password reset request for ' . trim(Yii::$app->sys->event_name))
            ->send();
    }
}
