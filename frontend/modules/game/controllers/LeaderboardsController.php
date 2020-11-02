<?php

namespace app\modules\game\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class LeaderboardsController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          'access' => [
            'class' => AccessControl::class,
            'only' => ['index'],
            'rules' => [
                [
                    'actions' => ['index'],
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return !Yii::$app->DisabledRoute->disabled($action);
                    },
                ],
            ],
          ],
        ];
    }

    public function actionIndex()
    {
      $command=\Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type', 'offense');
      $totalPoints=$command->queryScalar();

      $playerDataProvider=new ActiveDataProvider([
        'query' => \app\models\PlayerRank::find()->limit(10)->orderBy(['id'=>SORT_ASC, 'player_id'=>SORT_ASC]),
        'pagination' => false,
      ]);

      $headshotDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->timed()->limit(10)->orderBy(['timer'=>SORT_ASC,'created_at'=>SORT_ASC]),
        'pagination' => false,
      ]);

      $solversDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->timed()->limit(10)->orderBy(['challenge_solver.timer'=>SORT_ASC,'challenge_solver.created_at'=>SORT_ASC]),
        'pagination' => false,
      ]);

      $mostSolvesDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->select(['*, COUNT(*) as timer'])->limit(10)->groupBy(['player_id'])->orderBy(['timer'=>SORT_DESC,'created_at'=>SORT_ASC]),
        'pagination' => false,
      ]);


      $AvgSolvesDataProvider=new ActiveDataProvider([
        'query' => \app\modules\challenge\models\ChallengeSolver::find()->timed()->select(['challenge_solver.player_id,avg(challenge_solver.timer) as timer'])->limit(10)->groupBy(['player_id'])->orderBy(['timer'=>SORT_ASC,'player_id'=>SORT_ASC]),
        'pagination' => false,
      ]);

      $AvgHeadshotDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->select(['headshot.player_id,avg(headshot.timer) as timer'])->timed()->limit(10)->groupBy(['player_id'])->having('count(distinct target_id)>1')->orderBy(['timer'=>SORT_ASC,'player_id'=>SORT_ASC]),
        'pagination' => false,
      ]);


      $mostHeadshotsDataProvider=new ActiveDataProvider([
        'query' => \app\modules\game\models\Headshot::find()->select(['*, COUNT(*) as timer'])->limit(10)->groupBy(['player_id'])->orderBy(['timer'=>SORT_DESC,'created_at'=>SORT_ASC]),
        'pagination' => false,
      ]);

        return $this->render('index',[
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
