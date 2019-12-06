<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\models\PlayerScore;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
      $command = Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type','offense');
      $totalPoints = $command->queryScalar();

      $targetProvider = new ActiveDataProvider([
          'query' => Target::find()->orderBy(['ip'=>SORT_ASC,'ts'=>SORT_DESC]),
          'pagination' => [
              'pageSizeParam'=>'target-perpage',
              'pageParam'=>'target-page',
              'pageSize' => 8,
          ]

      ]);
      $scoreProvider = new ActiveDataProvider([
          'query' => PlayerScore::find()->orderBy(['points'=>SORT_DESC,'player_id'=>SORT_ASC])->limit(100),
          'totalCount' => 100,
          'pagination' => [
              'pageSizeParam'=>'score-perpage',
              'pageParam'=>'score-page',
              'pageSize' => 10,
          ]
      ]);
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
          'targetProvider' => $targetProvider,
          'scoreProvider'=>$scoreProvider,
          'totalPoints'=>$totalPoints,
          'streamProvider'=>$streamProvider,
      ]);
    }

}
