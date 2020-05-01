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
        $player = Player::findOne([
            'email' => $this->email,
            'status' => Player::STATUS_INACTIVE
        ]);

        if ($player === null)
        {
            return false;
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
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName . ' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Account registration for ' . trim(Yii::$app->sys->event_name))
            ->send();
    }
}
