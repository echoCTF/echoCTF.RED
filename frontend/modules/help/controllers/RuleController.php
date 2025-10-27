<?php

namespace app\modules\help\controllers;

use Yii;
use app\modules\help\models\Rule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;

/**
 * RuleController implements the CRUD actions for Rule model.
 */
class RuleController extends \app\components\BaseController
{
  public function behaviors()
  {
    $parent = parent::behaviors();
    unset($parent['access']['rules']['teamsAccess']);
    unset($parent['access']['rules']['eventStartEnd']);
    unset($parent['access']['rules']['eventStart']);
    unset($parent['access']['rules']['eventEnd']);
    unset($parent['access']['rules']['eventActive']);


    return ArrayHelper::merge($parent, [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          'disabledRoute' => [
            'allow' => false,
            'matchCallback' => function ($rule, $action) {
              return Yii::$app->DisabledRoute->disabled($action);
            },
            'denyCallback' => function () {
              throw new \yii\web\HttpException(404, \Yii::t('app', 'This area is disabled.'));
            },
          ],
          [
            'allow' => true,
          ],
        ],
      ],
      'rateLimiter' => [
        'class' => \yii\filters\RateLimiter::class,
        'enableRateLimitHeaders' => true,
      ],
    ]);
  }

  /**
   * Lists all Rule models.
   * @return mixed
   */
  public function actionIndex()
  {
    $dataProvider = new ActiveDataProvider([
      'query' => Rule::find()->forPlayerType()->orderBy(['weight' => SORT_ASC, 'id' => SORT_ASC]),
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
    ]);
  }
}
