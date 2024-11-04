<?php

namespace app\modules\api\models;

use Yii;

class TargetInstance extends \app\modules\target\models\TargetInstance
{
  public $hostname;
  public $fqdn;
  public $owner;
  public $ipstr;
  public static function find()
  {
    return new TargetInstanceQuery(get_called_class());
  }

  public function fields()
  {
    return [
      'hostname',
      'fqdn',
      'target_id',
      'owner',
      'ip',
      'ipstr'
    ];
  }

  public function extraFields()
  {
    return [];
  }
}
