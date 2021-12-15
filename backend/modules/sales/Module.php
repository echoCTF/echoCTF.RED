<?php

namespace app\modules\sales;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

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
        \Yii::configure($this, require __DIR__ . '/config/web.php');
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
        	$this->controllerNamespace = 'app\modules\sales\commands';
        }
    }
}
