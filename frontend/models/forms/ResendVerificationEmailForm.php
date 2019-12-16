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

        if ($player === null) {
            return false;
        }
        return true;
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
