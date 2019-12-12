<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;

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

        if (!$player) {
            return false;
        }

        if (!Player::isPasswordResetTokenValid($player->password_reset_token)) {
            $player->generatePasswordResetToken();
            if (!$player->save()) {
                return false;
            }
        }
        return true;
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }
}
