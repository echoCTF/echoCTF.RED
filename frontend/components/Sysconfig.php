<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Sysconfig extends Component
{
  public function __get($attribute)
  {
  if(!(\Yii::$app->cache instanceof \yii\caching\MemCache))
    throw new \LogicException('Memcache not initialized.');

  $val=Yii::$app->cache->memcache->get('sysconfig:'.$attribute);
  // key not found
  if($val===false || $val==="0")
    return false;
  elseif($val==="1")
    return true;
  return $val;
  }

}
