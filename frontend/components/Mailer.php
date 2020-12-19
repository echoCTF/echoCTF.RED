<?php
namespace app\components;
use Yii;
class Mailer extends \yii\swiftmailer\Mailer
{
  public function init()
  {
    $this->useFileTransport=Yii::$app->sys->mail_useFileTransport;
    if(Yii::$app->sys->mail_host !== false)
      $this->transport->setHost(Yii::$app->sys->mail_host);
    if(Yii::$app->sys->mail_port !== false)
      $this->transport->setPort(Yii::$app->sys->mail_port);

    if(Yii::$app->sys->mail_username !== false)
      $this->transport->setUserName(Yii::$app->sys->mail_username);

    if(Yii::$app->sys->mail_password !== false)
      $this->transport->setPassword(Yii::$app->sys->mail_password);

    parent::init();

  }
}
