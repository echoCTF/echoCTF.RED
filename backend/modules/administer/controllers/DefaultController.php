<?php

namespace app\modules\administer\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * Default controller for the `administer` module
 */
class DefaultController extends \app\components\BaseController
{
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => \yii\filters\AccessControl::class,
        'rules' => [
          'authActions' => [
            'allow' => \Yii::$app->user->identity && \Yii::$app->user->identity->isAdmin,
            'actions' => ['index'],
            'roles' => ['@'],
          ],
        ],
      ],
    ]);
  }

  /**
   * Renders the index view for the module
   * @return string
   */
  public function actionIndex()
  {
    return $this->render('index');
  }
}
