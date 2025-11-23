<?php

namespace app\modules\subscription\controllers;

use Yii;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

use \app\modules\subscription\models\PlayerSubscription;
use \app\modules\subscription\models\PlayerProduct;
use \app\modules\subscription\models\Customer;
use \app\modules\subscription\models\Product;
use \app\modules\subscription\models\Price;
use \app\modules\subscription\models\InquiryForm;

/**
 * Perk controller for the `subscription` module
 */
class PerkController extends \app\components\BaseController
{

  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['configure',],
        'rules' => [
          [
            'allow' => false,
            'actions' => ['configure'],
            'matchCallback' => function () {
              return \Yii::$app->sys->subscriptions_emergency_suspend === true || \Yii::$app->sys->subscriptions_menu_show !== true;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is temporarily disabled, please try again in a couple of hours.'));
              return  \Yii::$app->getResponse()->redirect([\Yii::$app->sys->default_homepage]);
            }
          ],
          [
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ]);
  }


  public function actions()
  {
    $actions = parent::actions();
    return $actions;
  }


  public function actionConfigure($id)
  {
    try {
      $model = $this->findModel($id);
      $view = $model->product->shortcode;
      $viewPath = $this->getViewPath() . DIRECTORY_SEPARATOR . $view . '.php';
      if (file_exists($viewPath)) {
        return $this->render($view, [
          'model' => $model
        ]);
      }
      throw new \yii\web\NotFoundHttpException("Perk is not configurable!");
    } catch (\Exception $e) {
      \Yii::$app->session->setFlash('error', $e->getMessage());
    }
    return $this->redirect(['/subscription/default/index']);
  }

  protected function findModel($id)
  {
    if (($model = PlayerProduct::find()->mine()->andWhere(['product_id' => $id])->active()->one()) !== null) {
      return $model;
    }

    throw new \yii\web\NotFoundHttpException(\Yii::t('app', 'The requested product does not exist.'));
  }
}
