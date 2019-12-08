<?php

namespace app\modules\target\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Finding;
use app\modules\target\models\Treasure;
use app\models\PlayerFinding;
use app\models\PlayerTreasure;

/**
 * Default controller for the `target` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($id)
    {
      $target=$this->findModel($id);
      $sum=0;
      $userTarget=null;
      if(!Yii::$app->user->isGuest)
      {
        $userTarget=Target::findBySql('SELECT t.*, count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id WHERE t.id=:target_id GROUP BY t.id ORDER BY t.ip,t.fqdn,t.name')->params([':player_id'=>\Yii::$app->user->id,':target_id'=>$id])->one();
        //die(var_dump($userTarget->createCommand()->getRawSql()));
        $PF=PlayerFinding::find()->joinWith(['finding'])->where(['player_id'=>Yii::$app->user->id,'finding.target_id'=>$id])->all();
        $PT=PlayerTreasure::find()->joinWith(['treasure'])->where(['player_id'=>Yii::$app->user->id,'treasure.target_id'=>$id])->all();
        foreach($PF as $pf)
          $sum+=$pf->finding->points;
        foreach($PT as $pt)
          $sum+=$pt->treasure->points;
      }
      $treasures=$findings=[];
      foreach($target->treasures as $treasure)
        $treasures[]=$treasure->id;
      foreach($target->findings as $finding)
        $findings[]=$finding->id;
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['model_id'=>$findings, 'model'=>'finding'])
      ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])->orderBy(['ts'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
          ]);



        $headshotsProvider = new ArrayDataProvider([
            'allModels' => $target->headshots,
            'pagination' => [
                'pageSizeParam'=>'headshot-perpage',
                'pageParam'=>'headshot-page',
                'pageSize' => 10,
            ]]);

        return $this->render('index', [
            'target' => $target,
            'userTarget'=>$userTarget,
            'streamProvider'=>$dataProvider,
            'playerPoints'=>$sum,
            'headshotsProvider'=>$headshotsProvider
        ]);
    }
    /**
     * Finds the Target model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Target the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = \app\modules\target\models\Target::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
