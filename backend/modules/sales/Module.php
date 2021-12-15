<?php

namespace app\modules\sales;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;
/**
 * sales module definition class
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\sales\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        \Yii::configure($this, require __DIR__ . '/config/main.php');

    }


    public function bootstrap($app)
    {
      if ($app instanceof \yii\web\Application) {
        \Yii::configure($this, require __DIR__ . '/config/web.php');
        $app->getUrlManager()->addRules($this->components['urlManager']['rules'], false);
      }
      elseif ($app instanceof \yii\console\Application)
      {
          \Yii::configure($this, require __DIR__ . '/config/console.php');
          $this->controllerNamespace = 'app\modules\sales\commands';
          $app->controllerMap=ArrayHelper::merge($app->controllerMap, $this->controllerMap);
      }
    }
}
