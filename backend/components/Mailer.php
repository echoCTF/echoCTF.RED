<?php
namespace app\components;
use Yii;
class Mailer extends \yii\swiftmailer\Mailer
{
  public function init()
  {
    parent::init();

    $this->useFileTransport=Yii::$app->sys->mail_useFileTransport;
    $this->enableSwiftMailerLogging=true;
    
    if(Yii::$app->sys->mail_host !== false)
    {
      $this->transport->setHost(Yii::$app->sys->mail_host);
    }

    if(Yii::$app->sys->mail_port !== false)
    {
      $this->transport->setPort(Yii::$app->sys->mail_port);
    }

    if(Yii::$app->sys->mail_username !== false)
    {
      $this->transport->setUserName(Yii::$app->sys->mail_username);
    }

    if(Yii::$app->sys->mail_password !== false)
    {
      $this->transport->setPassword(Yii::$app->sys->mail_password);
    }

    if(Yii::$app->sys->mail_encryption !== false)
    {
      $this->transport->setEncryption(Yii::$app->sys->mail_encryption);
    }
    if(Yii::$app->sys->mail_verify_peer !== false)
    {
      $this->ssl['verify_peer']=Yii::$app->sys->mail_verify_peer;
    }
    if(Yii::$app->sys->mail_verify_peer_name !== false)
    {
      $this->ssl['verify_peer_name']=Yii::$app->sys->mail_verify_peer_name;
    }

  }
}
