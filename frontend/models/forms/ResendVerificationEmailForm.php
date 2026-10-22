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
  public $captcha;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    $filter = ['status' => [Player::STATUS_INACTIVE, Player::STATUS_UNVERIFIED]];
    if (\Yii::$app->sys->player_require_approval)
      $filter = ['status' => [Player::STATUS_INACTIVE, Player::STATUS_UNVERIFIED], 'approval' => [1, 2]];
    return [
      ['email', 'trim'],
      ['email', 'required'],
      ['email', 'email'],
      [
        'email',
        'exist',
        'targetClass' => '\app\models\Player',
        'filter' => $filter,
        'message' => \Yii::t('app', 'There is no user with this email address.')
      ],
      ['captcha', 'captcha'],
    ];
  }

  /**
   * Sends confirmation email to user
   *
   * @return bool whether the email was sent
   */
  public function sendEmail()
  {
    $verification_resend_ip = intval(Yii::$app->cache->memcache->get('verification_resend_ip:' . \Yii::$app->request->userIp));
    $verification_resend_email = intval(Yii::$app->cache->memcache->get('verification_resend_email:' . $this->email));
    if ((Yii::$app->sys->verification_resend_ip !== false && $verification_resend_ip >= intval(\Yii::$app->sys->verification_resend_ip)) || (Yii::$app->sys->verification_resend_email !== false && $verification_resend_email >= Yii::$app->sys->verification_resend_email)) {
      $this->addError('email', \Yii::t('app', 'Too many resend verification attempts. Please wait and try again.'));
      return false;
    }

    if (($player = Player::findOne(['email' => $this->email, 'status' => [Player::STATUS_INACTIVE, Player::STATUS_UNVERIFIED]])) === null) {
      return false;
    }

    if ($player->verification_token === null) {
      $player->generateEmailVerificationToken();
    }
    $mc = Yii::$app->cache->memcache;
    if (!$mc->add('verification_resend_ip:' . \Yii::$app->request->userIp, 1, Yii::$app->sys->verification_resend_ip_timeout))
      $mc->increment('verification_resend_ip:' . \Yii::$app->request->userIp);
    if (!$mc->add('verification_resend_email:' . $this->email, 1, Yii::$app->sys->verification_resend_email_timeout))
      $mc->increment('verification_resend_email:' . $this->email);
    if ($player->status === Player::STATUS_UNVERIFIED) {
      $name = 'emailChangeVerify';
      $subject = \Yii::t('app', 'Verify your email for {event_name}', ['event_name' => trim(Yii::$app->sys->event_name)]);
    } else {
      $name = 'emailVerify';
      $subject = \Yii::t('app', 'Account registration for {event_name}', ['event_name' => trim(Yii::$app->sys->event_name)]);
    }
    $verifyLink = \Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $player->verification_token]);
    return $player->mailTemplate($name, $subject, ['verifyLink' => $verifyLink]);
  }
}
