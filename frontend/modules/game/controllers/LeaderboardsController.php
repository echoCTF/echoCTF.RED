<?php

namespace app\modules\game\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
class LeaderboardsController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[
        'access' => [
          'class' => AccessControl::class,
          'rules' => [
              'eventStartEnd'=>[
                'actions' => [''],
              ],
              'eventEnd'=>[
                'actions' => [''],
              ],
              'eventStart'=>[
                'actions' => [''],
              ],
              [
                  'allow' => true,
                  'roles'=>['@']
              ],
          ],
      ]]);

    }

    public function actionIndex()
    {
      $command=\Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type', 'offense');
      $totalPoints=$command->queryScalar();

      $playerDataProvider=new ActiveDataProvider([
        'query' => \app\models\PlayerRank::find()->orderBy(['id'=>SORT_ASC, 'player_id'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'playerRank-perpage',
          'pageParam'=>'playerRank-page',
          'pageSize'=>10
        ]
      ]);

      $headshotDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->timed()->orderBy(['timer'=>SORT_ASC,'created_at'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'headshotTimed-perpage',
          'pageParam'=>'headshotTimed-page',
          'pageSize'=>10
        ]
      ]);

      $solversDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->timed()->orderBy(['challenge_solver.timer'=>SORT_ASC,'challenge_solver.created_at'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'solverTimed-perpage',
          'pageParam'=>'solverTimed-page',
          'pageSize'=>10
        ]
      ]);

      $mostSolvesDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->select(['player_id, COUNT(*) as timer'])->groupBy(['player_id'])->orderBy(['timer'=>SORT_DESC,'created_at'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'solverMost-perpage',
          'pageParam'=>'solverMost-page',
          'pageSize'=>10
        ]
      ]);


      $AvgSolvesDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->timed()->select(['challenge_solver.player_id,avg(challenge_solver.timer) as timer'])->groupBy(['player_id'])->orderBy(['timer'=>SORT_ASC,'player_id'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'solverAvg-perpage',
          'pageParam'=>'solverAvg-page',
          'pageSize'=>10
        ]
      ]);

      $AvgHeadshotDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->select(['headshot.player_id,avg(headshot.timer) as timer'])->timed()->groupBy(['player_id'])->having('count(distinct target_id)>1')->orderBy(['timer'=>SORT_ASC,'player_id'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'headshotAvg-perpage',
          'pageParam'=>'headshotAvg-page',
          'pageSize'=>10
        ]
      ]);


      $mostHeadshotsDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->select(['player_id, COUNT(*) as timer'])->groupBy(['player_id'])->orderBy(['timer'=>SORT_DESC,'created_at'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'headshotMost-perpage',
          'pageParam'=>'headshotMost-page',
          'pageSize'=>10
        ]
      ]);
      $teamDataProvider=new ActiveDataProvider([
        'query' => \app\modules\team\models\TeamRank::find()->orderBy(['id'=>SORT_ASC, 'team_id'=>SORT_ASC]),
        'pagination'=> [
          'pageSizeParam'=>'teamRank-perpage',
          'pageParam'=>'teamRank-page',
          'pageSize'=>10
        ]
      ]);

        return $this->render('index',[
          'teamDataProvider'=>$teamDataProvider,
          'playerDataProvider'=>$playerDataProvider,
          'headshotDataProvider'=>$headshotDataProvider,
          'mostHeadshotsDataProvider'=>$mostHeadshotsDataProvider,
          'AvgHeadshotDataProvider'=>$AvgHeadshotDataProvider,
          'solversDataProvider'=>$solversDataProvider,
          'mostSolvesDataProvider'=>$mostSolvesDataProvider,
          'AvgSolvesDataProvider'=>$AvgSolvesDataProvider,
          'totalPoints' => $totalPoints
        ]);
    }

}
