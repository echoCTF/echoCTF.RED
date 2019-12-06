<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Sysconfig extends Component
{
 public function __get($attribute)
 {
  return Yii::$app->cache->Memcache->get('sysconfig:'.$attribute);
 }

}
