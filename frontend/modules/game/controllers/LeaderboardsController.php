<?php

namespace app\modules\game\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class LeaderboardsController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['index',],
        'rules' => [
          'teamsAccess' => [
            'actions' => [''],
          ],
          'eventStartEnd' => [
            'actions' => [''],
          ],
          'eventEnd' => [
            'actions' => [''],
          ],
          'eventStart' => [
            'actions' => [''],
          ],
          'LeaderboardBeforeStart' => [
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->event_start !== false && time() < \Yii::$app->sys->event_start && !\Yii::$app->sys->leaderboard_visible_before_event_start;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled before the start of the competition'));
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],
          'LeaderboardAfterEnd' => [
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->event_end !== false && time() > \Yii::$app->sys->event_end && !\Yii::$app->sys->leaderboard_visible_after_event_end;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled after the competition ends'));
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],

          [
            'allow' => true,
            'roles' => ['@']
          ],
          [
            'allow' => true,
            'roles' => ['?'],
            'matchCallback' => function () {
              return \Yii::$app->sys->guest_visible_leaderboards;
            },
          ],

        ],
      ]
    ]);
  }

  public function actionIndex()
  {
    if (Yii::$app->user->isGuest) {
      $academic = 0;
    } else {
      $academic = Yii::$app->user->identity->academic;
    }

    $command = \Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
    $command->bindValue(':player_type', 'offense');
    $totalPoints = $command->queryScalar();
    $total_targets=intval(\app\modules\target\models\Target::find()->active()->count());
    $total_challenges=intval(\app\modules\challenge\models\Challenge::find()->alias('t')->active()->count());

    if (\Yii::$app->sys->leaderboard_show_zero) {
      $PR = \app\models\PlayerRank::find()->academic($academic)->orderBy(['id' => SORT_ASC, 'player_id' => SORT_ASC]);
    } else {
      $PR = \app\models\PlayerRank::find()->academic($academic)->nonZero()->orderBy(['id' => SORT_ASC, 'player_id' => SORT_ASC]);
    }

    $playerDataProvider = new ActiveDataProvider([
      'query' => $PR,
      'pagination' => [
        'pageSizeParam' => 'playerRank-perpage',
        'pageParam' => 'playerRank-page',
        'pageSize' => 10
      ]
    ]);

    $headshotDataProvider = new ActiveDataProvider([
      'query' => \app\modules\game\models\Headshot::find()->academic($academic)->timed()->orderBy(['timer' => SORT_ASC, 'created_at' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'headshotTimed-perpage',
        'pageParam' => 'headshotTimed-page',
        'pageSize' => 10
      ]
    ]);

    $solversDataProvider = new ActiveDataProvider([
      'query' => \app\modules\challenge\models\ChallengeSolver::find()->academic($academic)->timed()->orderBy(['challenge_solver.timer' => SORT_ASC, 'challenge_solver.created_at' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'solverTimed-perpage',
        'pageParam' => 'solverTimed-page',
        'pageSize' => 10
      ]
    ]);

    $mostSolvesDataProvider = new ActiveDataProvider([
      'query' => \app\modules\challenge\models\ChallengeSolver::find()->academic($academic)->select(['player_id, COUNT(*) as timer'])->groupBy(['player_id'])->orderBy(['timer' => SORT_DESC, 'challenge_solver.created_at' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'solverMost-perpage',
        'pageParam' => 'solverMost-page',
        'pageSize' => 10
      ]
    ]);

    $mostWriteupsDataProvider = new ActiveDataProvider([
      'query' => \app\modules\target\models\Writeup::find()->totals(),
      'pagination' => [
        'pageSizeParam' => 'writeupsMost-perpage',
        'pageParam' => 'writeupsMost-page',
        'pageSize' => 10
      ]
    ]);

    $AvgSolvesDataProvider = new ActiveDataProvider([
      'query' => \app\modules\challenge\models\ChallengeSolver::find()->academic($academic)->timed()->select(['challenge_solver.player_id,avg(challenge_solver.timer) as timer'])->groupBy(['player_id'])->orderBy(['timer' => SORT_ASC, 'player_id' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'solverAvg-perpage',
        'pageParam' => 'solverAvg-page',
        'pageSize' => 10
      ]
    ]);

    $AvgHeadshotDataProvider = new ActiveDataProvider([
      'query' => \app\modules\game\models\Headshot::find()->academic($academic)->select(['headshot.player_id,avg(headshot.timer) as timer'])->timed()->groupBy(['player_id'])->having('count(distinct target_id)>1')->orderBy(['timer' => SORT_ASC, 'player_id' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'headshotAvg-perpage',
        'pageParam' => 'headshotAvg-page',
        'pageSize' => 10
      ]
    ]);


    $mostHeadshotsDataProvider = new ActiveDataProvider([
      'query' => \app\modules\game\models\Headshot::find()->academic($academic)->select(['player_id, COUNT(*) as timer'])->groupBy(['player_id'])->orderBy(['timer' => SORT_DESC, 'created_at' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'headshotMost-perpage',
        'pageParam' => 'headshotMost-page',
        'pageSize' => 10
      ]
    ]);

    $teamDataProvider = new ActiveDataProvider([
      'query' => \app\modules\team\models\TeamRank::find()->academic($academic)->orderBy(['id' => SORT_ASC, 'team_id' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'teamRank-perpage',
        'pageParam' => 'teamRank-page',
        'pageSize' => 10
      ]
    ]);

    $playerCountryDataProvider = new ActiveDataProvider([
      'query' => \app\models\PlayerCountryRank::find()->select('country,count(*) as counter')->where(['!=', 'country', 'UNK'])->groupBy('country')->orderBy(['counter' => SORT_DESC, 'country' => SORT_ASC]),
      'pagination' => [
        'pageSizeParam' => 'playerCountryRank-perpage',
        'pageParam' => 'playerCountryRank-page',
        'pageSize' => 10
      ]
    ]);

    $playerMonthlyDataProvider = new ActiveDataProvider([
      'query' => \app\modules\game\models\PlayerScoreMonthly::find()->currentMonth()->nonZero()->ordered(),
      'pagination' => [
        'pageSizeParam' => 'playerMonthlyRank-perpage',
        'pageParam' => 'playerMonthlyRank-page',
        'pageSize' => 10
      ]
    ]);
    return $this->render('index', [
      'total_targets'=>$total_targets,
      'total_challenges'=>$total_challenges,
      'mostWriteupsDataProvider'=>$mostWriteupsDataProvider,
      'playerMonthlyDataProvider'=>$playerMonthlyDataProvider,
      'teamDataProvider' => $teamDataProvider,
      'playerCountryDataProvider' => $playerCountryDataProvider,
      'playerDataProvider' => $playerDataProvider,
      'headshotDataProvider' => $headshotDataProvider,
      'mostHeadshotsDataProvider' => $mostHeadshotsDataProvider,
      'AvgHeadshotDataProvider' => $AvgHeadshotDataProvider,
      'solversDataProvider' => $solversDataProvider,
      'mostSolvesDataProvider' => $mostSolvesDataProvider,
      'AvgSolvesDataProvider' => $AvgSolvesDataProvider,
      'totalPoints' => $totalPoints
    ]);
  }
}
