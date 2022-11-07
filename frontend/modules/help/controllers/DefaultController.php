<?php

namespace app\modules\help\controllers;

use Yii;
use app\modules\help\models\Faq;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class DefaultController extends Controller
{
  public function behaviors()
  {
      return [
        'access' => [
              'class' => \yii\filters\AccessControl::class,
              'rules' => [
                'disabledRoute'=>[
                    'allow' => false,
                    'matchCallback' => function ($rule, $action) {
                      return Yii::$app->DisabledRoute->disabled($action);
                    },
                    'denyCallback' => function() {
                      throw new \yii\web\HttpException(404,\Yii::t('app','This area is disabled.'));
                    },
                ],
                [
                   'allow' => true,
                ],
            ],
          ],
      ];
  }

    /**
     * Lists all available help related modules
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

}
