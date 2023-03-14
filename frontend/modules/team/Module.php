<?php

namespace app\modules\team;

use app\overloads\yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

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
              throw new NotFoundHttpException(\Yii::t('app','Team module is disabled.'));
            }
          ],
          [
            'allow'=>true
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
