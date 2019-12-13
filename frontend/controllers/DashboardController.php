<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Treasure;
use app\models\PlayerTreasure;
use app\models\PlayerScore;
use yii\helpers\ArrayHelper;
class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
      $command = Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type','offense');
      $totalPoints = $command->queryScalar();
      $treasureStats=new \stdClass();
      $treasureStats->total=(int)Treasure::find()->count();
      $treasureStats->claims=(int)PlayerTreasure::find()->count();
      $treasureStats->claimed=(int)PlayerTreasure::find()->where(['player_id'=>Yii::$app->user->id])->count();
      $totalHeadshots=0;
      $tmod=Target::find()->active();
      foreach($tmod->all() as $model)
      {
        $totalHeadshots+=count($model->getHeadshots());
      }

      $userHeadshots=Target::findBySql('SELECT t.*,inet_ntoa(t.ip) as ipoctet,count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING player_treasures=total_treasures and player_findings=total_findings ORDER BY t.ip,t.fqdn,t.name')
        ->params([':player_id'=>\Yii::$app->user->id])->all();
      //die(var_dump($userHeadshots->createCommand()->getRawSql()));
      //die(var_dump($tmod->player_progress(Yii::$app->user->id)->one()));

      $scoreProvider = new ActiveDataProvider([
          'query' => PlayerScore::find()->orderBy(['points'=>SORT_DESC,'player_id'=>SORT_ASC])->limit(100),
          //'totalCount' => 100,
          'pagination' => [
              'pageSizeParam'=>'score-perpage',
              'pageParam'=>'score-page',
              'pageSize' => 11,
          ]
      ]);
      if(Yii::$app->request->get('score-page')===null)
        $scoreProvider->pagination->page = intval(Yii::$app->user->identity->profile->rank->id/11);

      $stream=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->orderBy(['ts'=>SORT_DESC]);
      $streamProvider = new ActiveDataProvider([
          'query' => $stream,
          'pagination' => [
              'pageSizeParam'=>'stream-perpage',
              'pageParam'=>'stream-page',
              'pageSize' => 10,
          ]
        ]);
      return $this->render('index', [
          //'targetProvider' => $targetProvider,
          'scoreProvider'=>$scoreProvider,
          'totalPoints'=>$totalPoints,
          'streamProvider'=>$streamProvider,
          'treasureStats'=>$treasureStats,
          'totalHeadshots'=>$totalHeadshots,
          'userHeadshots'=>$userHeadshots,
      ]);
    }

}
