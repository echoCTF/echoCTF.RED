<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Treasure;
use app\modules\game\models\Headshot;
use app\models\PlayerTreasure;
use app\models\PlayerScore;
use app\models\Profile;
use app\models\News;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DashboardController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          'eventActive' => [
            'actions' => [''],
          ],
          'eventStartEnd' => [
            'actions' => [''],
          ],
          'eventEnd' => [
            'actions' => [''],
          ],
          'teamsAccess' => [
            'actions' => ['']
          ],
          'eventStart' => [
            'actions' => [''],
          ],
          [
            'allow' => true,
            'roles' => ['@']
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [],
      ],
    ]);
  }

  public function actionIndex()
  {
    $dashboardStats = new \stdClass();
    $active_targets = Yii::$app->cache->getOrSet('active_targets', fn() => intval(\app\modules\target\models\Target::find()->active()->count()), 300);
    $active_challenges = Yii::$app->cache->getOrSet('active_challenges', fn() => intval(\app\modules\challenge\models\Challenge::find()->alias('t')->active()->count()), 300);
    $dashboardStats->countries = Yii::$app->cache->getOrSet('stats.countries', fn() => (int) Profile::find()->joinWith(['owner'])->select(['country'])->distinct()->where(['player.status' => 10])->count(), 300);
    if (Yii::$app->sys->academic_grouping !== false)
      $dashboardStats->claims = Yii::$app->cache->getOrSet('stats.claims_' . Yii::$app->user->identity->academic, fn() => (int) PlayerTreasure::find()->where(['player.academic' => Yii::$app->user->identity->academic])->joinWith(['player'])->count(), 60);
    else
      $dashboardStats->claims = Yii::$app->cache->getOrSet('stats.claims', fn() => (int) PlayerTreasure::find()->count(), 60);

    $dayActivity = null;
    if (Yii::$app->sys->dashboard_graph_visible) {
      $since = new \yii\db\Expression('now()-interval 10 day');
      $overall = Yii::$app->cache->getOrSet('dashboard.graph', fn() => (new \yii\db\Query())
        ->select(['d' => 'date(ts)', 'dat' => 'date_format(ts,"%D")', 'cnt' => 'count(*)'])
        ->from('stream')
        ->where(['>=', 'ts', $since])
        ->groupBy(new \yii\db\Expression('date(ts)'))
        ->orderBy(new \yii\db\Expression('date(ts)'))
        ->all(), 60);
      $mine = (new \yii\db\Query())
        ->select(['d' => 'date(ts)', 'cnt' => 'count(*)'])
        ->from('stream')
        ->where(['player_id' => Yii::$app->user->id])
        ->andWhere(['>=', 'ts', $since])
        ->groupBy(new \yii\db\Expression('date(ts)'))
        ->indexBy('d')
        ->all();
      foreach ($overall as $row) {
        $dayActivity['labels'][] = "'" . $row['dat'] . "'";
        $dayActivity['overallSeries'][] = $row['cnt'];
        $dayActivity['playerSeries'][] = $mine[$row['d']]['cnt'] ?? 0;
      }
    }
    $visits = Yii::$app->session->get('last_targets_visited');
    if ($visits !== null && count($visits) > 0) {
      $visitsSTR = implode(',', $visits);
      $last_targets_visited = Target::find()->addSelect("id,name")
        ->active()
        ->andWhere(['IN', 'id', $visits])
        ->orderBy([new \yii\db\Expression("FIELD (id, $visitsSTR)")])
        ->asArray()->all();
    } else
      $last_targets_visited = [];
    $lastVisitsProvider = new ArrayDataProvider([
      'allModels' => $last_targets_visited,
      'sort' => [],
      'pagination' => [
        'pageSize' => 5,
      ],
    ]);


    $query = News::find();
    $pages = (Yii::$app->sys->dashboard_news_total_pages === false ? 42 : Yii::$app->sys->dashboard_news_total_pages);
    $recordsPerpage = (Yii::$app->sys->dashboard_news_records_per_page === false ? 4 : Yii::$app->sys->dashboard_news_records_per_page);
    $query->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->limit($pages * $recordsPerpage);
    $newsModel = News::find()
      ->from(['t' => $query])
      ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
    $newsProvider = new ActiveDataProvider([
      'query' => $newsModel,
      'pagination' => [
        'pageSizeParam' => 'news-perpage',
        'pageParam' => 'news-page',
        'pageSize' => $recordsPerpage,
      ],
      'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
    ]);

    return $this->render('index', [
      'totalPoints' => 0,
      'active_targets' => $active_targets,
      'active_challenges' => $active_challenges,
      'lastVisitsProvider' => $lastVisitsProvider,
      'dashboardStats' => $dashboardStats,
      'newsProvider' => $newsProvider,
      'dayActivity' => $dayActivity
    ]);
  }

  public function actionNews($id)
  {
    $query = News::find();
    $pages = (Yii::$app->sys->dashboard_news_total_pages === false ? 42 : Yii::$app->sys->dashboard_news_total_pages);
    $recordsPerpage = (Yii::$app->sys->dashboard_news_records_per_page === false ? 4 : Yii::$app->sys->dashboard_news_records_per_page);
    $query->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->limit($pages * $recordsPerpage);
    $newsModel = News::find()
      ->from(['t' => $query])
      ->where(['id' => $id])
      ->one();

    if ($newsModel === null) {
      Yii::$app->session->addFlash('warning', \Yii::t('app', 'No such news entry found!'));
      return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
    }
    return $this->render('news', [
      'model' => $newsModel,
      'full' => true
    ]);
  }
}
