<?php

namespace app\modules\api;

/**
 * API module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='\app\modules\api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
      parent::init();
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }
}
