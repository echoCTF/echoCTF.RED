<?php
namespace app\components;
use Yii;
class Mailer extends \yii\symfonymailer\Mailer
{
  public function init()
  {
    parent::init();

    $this->useFileTransport=Yii::$app->sys->mail_useFileTransport;

    $config['scheme']='smtp';
    $config['options']['local_domain']=\Yii::$app->sys->offense_domain;
    if(Yii::$app->sys->mail_host !== false)
    {
      $config['host']=Yii::$app->sys->mail_host;
    }

    if(Yii::$app->sys->mail_port !== false)
    {
      $config['port']=intval(Yii::$app->sys->mail_port);
    }

    if(Yii::$app->sys->mail_username !== false)
    {
      $config['username']=Yii::$app->sys->mail_username;
    }

    if(Yii::$app->sys->mail_password !== false)
    {
      $config['password']=Yii::$app->sys->mail_password;
    }

    if(Yii::$app->sys->mail_encryption !== false && trim(Yii::$app->sys->mail_encryption)!='')
    {
      $config['scheme']=Yii::$app->sys->mail_encryption;
    }

    if(Yii::$app->sys->mail_verify_peer !== false)
    {
      $this->ssl['verify_peer']=Yii::$app->sys->mail_verify_peer;
    }
    if(Yii::$app->sys->mail_verify_peer_name !== false)
    {
      $this->ssl['verify_peer_name']=Yii::$app->sys->mail_verify_peer_name;
    }
    $this->setTransport($config);
  }
}
