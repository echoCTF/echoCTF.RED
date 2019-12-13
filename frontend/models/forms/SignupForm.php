<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use app\models\PlayerSsl;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
        }
        return $this->sendEmail($player);

    }

    /**
     * Sends confirmation email to player
     * @param User $player player model to with email should be send
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
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName . ' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject('Account registration at ' . trim(Yii::$app->sys->event_name))
            ->send();
    }
}
