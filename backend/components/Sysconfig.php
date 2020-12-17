<?php
namespace app\components;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;
use app\modules\settings\models\Sysconfig as dbSys;

class Sysconfig extends Component
{
  public function __get($attribute)
  {
    if(dbSys::findOne($attribute) === null) return null;

    $val=dbSys::findOne($attribute)->val;
    // key not found
    if($val === false || $val === "0")
      return false;
    elseif($val === "1")
      return true;
    return $val;
  }

  public static function mailerInit()
  {
    if(dbSys::findOne('mail_host'))
      \Yii::$app->mailer->transport->setHost(dbSys::findOne('mail_host')->val);

    if(dbSys::findOne('mail_port'))
      \Yii::$app->mailer->transport->setPort(dbSys::findOne('mail_port')->val);

    if(dbSys::findOne('mail_username'))
      \Yii::$app->mailer->transport->setUserName(dbSys::findOne('mail_username')->val);

    if(dbSys::findOne('mail_password'))
      \Yii::$app->mailer->transport->setPassword(dbSys::findOne('mail_password')->val);
  }

}
