<?php

namespace app\modules\restapi;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

/**
 * restapi module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\restapi\controllers';


    public function behaviors()
    {
        $behaviors=parent::behaviors();
        $behaviors['authenticator']=[
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                QueryParamAuth::class,
            ],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
      public function init()
      {
          parent::init();
          \Yii::$app->user->enableSession=false;
      }}
