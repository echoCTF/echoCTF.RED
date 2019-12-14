<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Sysconfig extends Component
{
 public function __get($attribute)
 {
  $val=Yii::$app->cache->Memcache->get('sysconfig:'.$attribute);
  // key not found
  if($val===false || $val==="0")
    return false;
  elseif($val==="1")
    return true;
  return $val;
 }

}
