<?php

namespace app\modules\help\controllers;

use Yii;
use app\models\Experience;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class ExperienceController extends \app\components\BaseController
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
   * Lists all Faq models.
   * @return mixed
   */
  public function actionIndex()
  {
    $dataProvider = new ActiveDataProvider([
      'query' => Experience::find()
        ->select(['experience.*', 'COUNT(p.player_id) AS player_count',])
        ->leftJoin(['p' => 'player_score'], 'p.points BETWEEN experience.min_points AND experience.max_points')
        ->groupBy(['experience.id', 'experience.name'])
        ->orderBy(['experience.id' => SORT_ASC]),
    ]);

    $totalPlayers = (int) \app\models\Player::find()->count();
    return $this->render('index', [
      'totalPlayers' => $totalPlayers,
      'dataProvider' => $dataProvider,
    ]);
  }
}
