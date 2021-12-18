<?php

namespace app\modules\target\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Finding;
use app\modules\target\models\Treasure;
use app\modules\target\models\PlayerTargetHelp as PTH;
use app\models\PlayerFinding;
use app\models\PlayerTreasure;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\game\models\Headshot;

/**
 * Default controller for the `target` module
 */
class DefaultController extends \app\components\BaseController
{

      public function actions()
      {
        $actions=parent::actions();
        $actions['spin']['class']='app\modules\target\actions\SpinRestAction';
        return $actions;
      }

      public function behaviors()
      {
          return ArrayHelper::merge(parent::behaviors(),[
              'access' => [
                  'class' => AccessControl::class,
                  'only' => ['index','view', 'claim', 'spin','versus','badge'],
                  'rules' => [
                       'eventStartEnd'=>[
                          'actions' => ['index', 'view','claim', 'spin','versus'],
                      ],
                      'teamsAccess'=>[
                        'actions' => ['index', 'claim', 'spin','versus'],
                      ],
                      'disabledRoute'=>[
                        'actions' => ['badge', 'view', 'index', 'claim', 'spin','versus'],
                      ],
                      [
                          'allow' => true,
                          'actions' => ['claim', 'spin'],
                          'roles' => ['@'],
                          'verbs'=>['post'],
                      ],
                      [
                          'actions'=>['index'],
                          'allow' => true,
                          'roles'=>['@']
                      ],
                      [
                        'actions' => ['claim','spin'],
                        'allow' => false,
                        'verbs' => ['POST'],
                        'roles'=>['@'],
                        'matchCallback' => function () {
                          return !\Yii::$app->request->validateCsrfToken(\Yii::$app->request->getBodyParam(\Yii::$app->request->csrfParam));
                        },
                      ],

                      [
                          'actions'=>['view','versus','badge'],
                          'allow' => true,
                          #'roles'=>['*']
                      ],
                  ],
              ],
              [
                'class' => 'yii\filters\AjaxFilter',
                'only' => ['claim']
              ],
          ]);
      }
    /**
     * Renders a target versus a profile
     *
     */
      public function actionVersus(int $id, int $profile_id)
      {
        $sum=0;
        $profile=$this->findProfile($profile_id);
        $this->checkVisible($profile);

        $target=Target::find()->where(['t.id'=>$id])->player_progress($profile->player_id)->one();
        $PF=PlayerFinding::find()->joinWith(['finding'])->where(['player_id'=>$profile->player_id, 'finding.target_id'=>$id])->all();
        $PT=PlayerTreasure::find()->joinWith(['treasure'])->where(['player_id'=>$profile->player_id, 'treasure.target_id'=>$id])->all();
        foreach($PF as $pf)
          $sum+=$pf->finding->points;
        foreach($PT as $pt)
          $sum+=$pt->treasure->points;
        $treasures=$findings=[];
        foreach($target->treasures as $treasure)
          $treasures[]=$treasure->id;
        foreach($target->findings as $finding)
          $findings[]=$finding->id;
        $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
        ->where(['model_id'=>$findings, 'model'=>'finding'])
        ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])
        ->orWhere(['model_id'=>$id, 'model'=>'headshot'])
        ->andWhere(['player_id'=>$profile->player_id])
        ;
        $dataProvider=new ActiveDataProvider([
              'query' => $model->orderBy(['ts'=>SORT_DESC, 'id'=>SORT_DESC]),
              'pagination' => [
                  'pageSizeParam'=>'stream-perpage',
                  'pageParam'=>'stream-page',
                  'pageSize' => 10,
              ]
        ]);

        $headshotsProvider=new ArrayDataProvider([
              'allModels' => $target->headshots,
              'pagination' => [
                  'pageSizeParam'=>'headshot-perpage',
                  'pageParam'=>'headshot-page',
                  'pageSize' => 10,
              ]]);

        return $this->render('versus', [
              'profile'=>$profile,
              'target' => $target,
              'streamProvider'=>$dataProvider,
              'playerPoints'=>$sum,
              'headshotsProvider'=>$headshotsProvider
          ]);

      }

    /**
     * Renders a Target model details view
     * @return string
     */
     public function actionIndex()
     {
       $command=Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
       $command->bindValue(':player_type', 'offense');
       $totalPoints=$command->queryScalar();
       $treasureStats=new \stdClass();
       $treasureStats->total=(int) Treasure::find()->count();
       $treasureStats->claims=(int) PlayerTreasure::find()->count();
       $treasureStats->claimed=(int) PlayerTreasure::find()->where(['player_id'=>Yii::$app->user->id])->count();
       $totalHeadshots=Headshot::find()->count();

       return $this->render('index', [
           'totalPoints'=>$totalPoints,
           'treasureStats'=>$treasureStats,
           'totalHeadshots'=>$totalHeadshots,
       ]);
     }

    /**
     * Renders a Target model details view
     * @return string
     */
    public function actionView(int $id)
    {
      $sum=0;
      $target=$this->findModel($id);
      if(!Yii::$app->user->isGuest)
      {
        $target=Target::find()->where(['t.id'=>$id])->player_progress((int) Yii::$app->user->id)->one();
        $PF=PlayerFinding::find()->select("sum(player_finding.points) as points")->joinWith(['finding'])->where(['player_id'=>Yii::$app->user->id, 'finding.target_id'=>$id])->one();
        $PT=PlayerTreasure::find()->select("sum(player_treasure.points) as points")->joinWith(['treasure'])->where(['player_id'=>Yii::$app->user->id, 'treasure.target_id'=>$id])->one();
        $sum=$PF->points+$PT->points;
      }
      $treasures=$findings=[];
      foreach($target->treasures as $treasure)
        $treasures[]=$treasure->id;
      foreach($target->findings as $finding)
        $findings[]=$finding->id;
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['model_id'=>$findings, 'model'=>'finding'])
      ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])
      ->orWhere(['model_id'=>$id, 'model'=>'headshot'])
      ->orderBy(['ts'=>SORT_DESC, 'id'=>SORT_DESC]);
      $dataProvider=new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
      ]);



      $headshotsProvider=new ArrayDataProvider([
            'allModels' => $target->headshots,
            'pagination' => [
                'pageSizeParam'=>'headshot-perpage',
                'pageParam'=>'headshot-page',
                'pageSize' => 10,
            ]]);

      return $this->render('view', [
            'target' => $target,
            'streamProvider'=>$dataProvider,
            'playerPoints'=>$sum,
            'headshotsProvider'=>$headshotsProvider
        ]);
    }

    /**
     * Claims a treasure flag for a target
     * @return string
     */
    public function actionClaim()
    {
        $string=Yii::$app->request->post('hash');

        if(!is_string($string) || trim($string)=="")
        {
          return $this->renderAjax('claim');
        }

        $treasure=Treasure::find()->claimable()->byCode($string)->one();

        if($treasure !== null && Treasure::find()->byCode($string)->claimable()->notBy((int) Yii::$app->user->id)->one() === null)
        {
          Yii::$app->session->setFlash('warning', sprintf('Flag [%s] claimed before', $treasure->name, $treasure->target->name));
          return $this->renderAjax('claim');
        }
        elseif($treasure === null)
        {
          Yii::$app->counters->increment('failed_claims');
          Yii::$app->session->setFlash('error', sprintf('Flag [<strong>%s</strong>] does not exist!', Html::encode($string)));
          return $this->renderAjax('claim');
        }
        try {
          $this->module->checkNetwork($treasure->target);
        }
        catch(\Throwable $e)
        {
          Yii::$app->session->setFlash('error', 'You cannot claim this flag. You dont have access to this network.');
          return $this->renderAjax('claim');
        }

        Yii::$app->counters->increment('claims');
        $this->doClaim($treasure);
        return $this->renderAjax('claim');
    }


    /**
    * Generate and display target badge with dynamic details
    */
    public function actionBadge(int $id)
    {
      $target=$this->findModel($id);
      $fname=Yii::getAlias(sprintf('@app/web/images/targets/%s.png',$target->name));
      $src = imagecreatefrompng($fname);
      if($src===false) return $this->redirect(['/']);

      imagealphablending($src, false);
      imagesavealpha($src, true);
      $textcolor = imagecolorallocate($src, 255, 255, 255);
      $consolecolor = imagecolorallocate($src, 148,148,148);
      $greencolor = imagecolorallocate($src, 148,193,31);
      if(Headshot::find()->where(['target_id'=>$target->id])->last()->one())
      {
        $lastHeadshot=Headshot::find()->where(['target_id'=>$target->id])->last()->one()->player->username;
//        $hs=Headshot::find()->target_avg_time($target->id)->one();
      }
      else
      {
        $lastHeadshot="none yet";
      }
      $lineheight=20;
      $i=3;
      imagestring($src, 5, 60, $lineheight*$i, sprintf("root@%s:/#",\Yii::$app->sys->offense_domain,$target->name),$consolecolor);
      imagestring($src, 5, 235, $lineheight*$i++, sprintf("./target --stats %s",$target->name),$textcolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("ipv4..........: %s",long2ip($target->ip)),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("fqdn..........: %s",$target->fqdn),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("points........: %s",number_format($target->points)),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("flags.........: %d",count($target->treasures)),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("services......: %d",count($target->findings)),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("headshots.....: %d",count($target->headshots)),$greencolor);
      imagestring($src, 5, 60, $lineheight*$i++, sprintf("last headshot.: %s",$lastHeadshot),$greencolor);
//      if($hs && $hs->average > 0 && $target->timer!==0)
//        imagestring($src, 6, 40, $lineheight*9, sprintf("avg headshot.: %s",\Yii::$app->formatter->asDuration($hs->average)),$greencolor);

      Yii::$app->getResponse()->getHeaders()
          ->set('Pragma', 'public')
          ->set('Expires', '0')
          ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
          ->set('Content-Transfer-Encoding', 'binary')
          ->set('Content-type', 'image/png');

      Yii::$app->response->format = Response::FORMAT_RAW;
      ob_start();
      imagepng($src);
      imagedestroy($src);
      return ob_get_clean();
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
        if(($model=\app\modules\target\models\Target::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested target does not exist.');
    }

    protected function findProfile($id)
    {
        if(($model=\app\models\Profile::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested profile does not exist.');
    }

    protected function checkVisible($profile)
    {
      if(!$profile->visible)
          return $this->redirect(['/']);
    }

    protected function doClaim($treasure)
    {
      $connection=Yii::$app->db;
      $transaction=$connection->beginTransaction();
      try
      {
        $PT=new PlayerTreasure();
        $PT->player_id=(int) Yii::$app->user->id;
        $PT->treasure_id=$treasure->id;
        $PT->save();
        if($treasure->appears !== -1)
        {
          $treasure->updateAttributes(['appears' => intval($treasure->appears) - 1]);
        }
        $transaction->commit();
        $this->doOndemand($treasure->target);
        $PT->refresh();
        Yii::$app->session->setFlash('success', sprintf('Flag [%s] claimed for %s points', $treasure->name, number_format($PT->points)));
      }
      catch(\Exception $e)
      {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', 'Flag failed');
        throw $e;
      }
      catch(\Throwable $e)
      {
        $transaction->rollBack();
        throw $e;
      }
    }
    protected function doOndemand($target)
    {
      if($target->ondemand && $target->ondemand->state>0)
      {
        $target->ondemand->updateAttributes(['heartbeat' => new \yii\db\Expression('NOW()')]);
      }
    }
}
