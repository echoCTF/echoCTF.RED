<?php

namespace app\components;

use Yii;

class Mailer extends \yii\symfonymailer\Mailer
{
  public function init()
  {
    parent::init();
    $this->useFileTransport = (bool)Yii::$app->sys->mail_useFileTransport;
    if (trim(\Yii::$app->sys->dsn)!=="") {
      $config['dsn']=trim(\Yii::$app->sys->dsn);
    } else {
      $config['dsn'] = Yii::t('app', '{mail_encryption}://{mail_userpass}{mail_host}:{mail_port}?verify_peer={verify_peer}&local_domain={local_domain}&verify_peer_name={verify_peer_name}', [
        'mail_encryption' => \Yii::$app->sys->mail_encryption ?? "smtp",
        'mail_userpass' => (trim(\Yii::$app->sys->mail_username) != "" && trim(\Yii::$app->sys->mail_password) != "") ? trim(\Yii::$app->sys->mail_username) . ':' . trim(\Yii::$app->sys->mail_password) . '@' : '',
        'mail_password' => \Yii::$app->sys->mail_password,
        'mail_host' => \Yii::$app->sys->mail_host,
        'mail_port' => \Yii::$app->sys->mail_port ?? "25",
        'verify_peer' => intval(\Yii::$app->sys->verify_peer),
        'local_domain' => \Yii::$app->sys->offense_domain,
        'verify_peer_name' => intval(\Yii::$app->sys->verify_peer_name),
      ]);
    }
    $this->setTransport($config);
  }
}
