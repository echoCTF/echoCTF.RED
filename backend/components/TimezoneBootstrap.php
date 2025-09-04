<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;

class TimezoneBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
      Yii::$app->timeZone = Yii::$app->sys->time_zone ?: 'UTC';
      date_default_timezone_set(Yii::$app->sys->time_zone ?: 'UTC');
    }
}
