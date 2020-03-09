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
  if(dbSys::findOne($attribute)=== null) return null;
  $val=dbSys::findOne($attribute)->val;
  // key not found
  if($val===false || $val==="0")
    return false;
  elseif($val==="1")
    return true;
  return $val;
 }

}
