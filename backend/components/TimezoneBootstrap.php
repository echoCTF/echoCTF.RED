<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;

class TimezoneBootstrap implements BootstrapInterface
{
  public function bootstrap($app)
  {
    try {
      $tz = Yii::$app->sys->time_zone ?: 'UTC';
    } catch (\Throwable $e) {
      $tz = 'UTC';
    }
    Yii::$app->timeZone = $tz;
    date_default_timezone_set($tz);
  }
}
