<?php

namespace app\modules\team;

use yii\filters\AccessControl;

/**
 * target module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\team\controllers';

  public function behaviors()
  {
    return [
      'access' => [
      'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => false,
            'matchCallback' => function ($rule, $action) {
              if(\Yii::$app->sys->teams===false)
              {
                 return true;
              }
              return false;
            },
            'denyCallback' => function() {
              return  \Yii::$app->getResponse()->redirect(['/site/index']);
            }
          ]
        ],
      ],
    ];
  }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
