<?php

namespace app\modules\target\controllers;

use Yii;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Finding;
use app\modules\target\models\Treasure;
use app\models\PlayerFinding;
use app\models\PlayerTreasure;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\modules\game\models\Headshot;
/**
 * Default controller for the `target` module
 */
class DefaultController extends Controller
{

      public function actions()
      {
        $actions = parent::actions();
        $actions['spin']['class'] = 'app\modules\target\actions\SpinRestAction';
        return $actions;
      }

      public function behaviors()
      {
          return [
              'access' => [
                  'class' => AccessControl::className(),
                  'only' => ['index', 'claim','spin'],
                  'rules' => [
                      [
                          'allow' => true,
                          'actions' => ['index'],
                      ],
                      [
                          'allow' => true,
                          'actions' => ['claim','spin'],
                          'roles' => ['@'],
                          'verbs'=>['post'],
                      ],
                  ],
              ],
              [
                'class' => 'yii\filters\AjaxFilter',
                'only' => ['claim']
              ],
          ];
      }
    /**
     * Renders a target versus a profile
     *
     */
     public function actionVersus(int $id,int $profile_id)
     {
       $sum=0;
       $userTarget=null;
       $profile=$this->findProfile($profile_id);
       if(Yii::$app->user->isGuest && $profile->visibility!='public') {
                			return $this->redirect(['/']);
       }

       if($profile->visibility!='public' && $profile->visibility!='ingame' && !Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin) {
                			return $this->redirect(['/']);
       }


       $target=Target::find()->where(['t.id'=>$id])->player_progress($profile->player_id)->one();
       $PF=PlayerFinding::find()->joinWith(['finding'])->where(['player_id'=>$profile->player_id,'finding.target_id'=>$id])->all();
       $PT=PlayerTreasure::find()->joinWith(['treasure'])->where(['player_id'=>$profile->player_id,'treasure.target_id'=>$id])->all();
       foreach($PF as $pf) {
                $sum+=$pf->finding->points;
       }
       foreach($PT as $pt) {
                $sum+=$pt->treasure->points;
       }
       $treasures=$findings=[];
       foreach($target->treasures as $treasure) {
                $treasures[]=$treasure->id;
       }
       foreach($target->findings as $finding) {
                $findings[]=$finding->id;
       }
       $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
       ->where(['model_id'=>$findings, 'model'=>'finding'])
       ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])
       ->orWhere(['model_id'=>$id, 'model'=>'headshot'])
       ->andWhere(['player_id'=>$profile->player_id])
       ;
       $dataProvider = new ActiveDataProvider([
             'query' => $model->orderBy(['ts'=>SORT_DESC]),
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
    public function actionIndex(int $id)
    {
      $sum=0;
      $userTarget=null;
      $target=$this->findModel($id);
      if(!Yii::$app->user->isGuest)
      {
        $target=Target::find()->where(['t.id'=>$id])->player_progress(Yii::$app->user->id)->one();
        $PF=PlayerFinding::find()->joinWith(['finding'])->where(['player_id'=>Yii::$app->user->id,'finding.target_id'=>$id])->all();
        $PT=PlayerTreasure::find()->joinWith(['treasure'])->where(['player_id'=>Yii::$app->user->id,'treasure.target_id'=>$id])->all();
        foreach($PF as $pf) {
                  $sum+=$pf->finding->points;
        }
        foreach($PT as $pt) {
                  $sum+=$pt->treasure->points;
        }
      }
      $treasures=$findings=[];
      foreach($target->treasures as $treasure) {
              $treasures[]=$treasure->id;
      }
      foreach($target->findings as $finding) {
              $findings[]=$finding->id;
      }
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['model_id'=>$findings, 'model'=>'finding'])
      ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])
      ->orWhere(['model_id'=>$id, 'model'=>'headshot'])
      ->orderBy(['ts'=>SORT_DESC,'id'=>SORT_DESC]);
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
            'streamProvider'=>$dataProvider,
            'playerPoints'=>$sum,
            'headshotsProvider'=>$headshotsProvider
        ]);
    }

    /**
     * Rate the difficulty of the given target
     */
/*    public function actionRate(int $id)
    {
         $model = Headshot::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id]);
         return $this->renderAjax('_rate_target', [
                  'model' => $model,
          ]);

    }
*/
    /**
     * Claims a treasure flag for a target
     * @return string
     */
    public function actionClaim()
    {
        $string = Yii::$app->request->post('hash');
        //$string = Yii::$app->request->get('hash');
        if(empty($string)) {
            return $this->renderAjax('claim');
        }
        $treasure=Treasure::find()->claimable()->byCode($string)->one();
        if($treasure!==null && Treasure::find()->byCode($string)->claimable()->notBy(Yii::$app->user->id)->one()===null)
        {
          Yii::$app->session->setFlash('warning',sprintf('Flag [%s] claimed before',$treasure->name,$treasure->target->name));
          return $this->renderAjax('claim');
        } elseif($treasure===null)
        {
          Yii::$app->session->setFlash('error',sprintf('Flag [<strong>%s</strong>] does not exist!',Html::encode($string)));
          return $this->renderAjax('claim');
        }

        $connection=Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
          if($treasure!==null)
          {
            $PT=new PlayerTreasure();
            $PT->player_id=Yii::$app->user->id;
            $PT->treasure_id=$treasure->id;
            $PT->save();
            if($treasure->appears!==-1)
            {
              $treasure->updateAttributes(['appears' => intval($treasure->appears)-1]);
            }
          }
          $transaction->commit();
          Yii::$app->session->setFlash('success',sprintf('Flag [%s] claimed for %s points',$treasure->name,number_format($treasure->points)));
        } catch (\Exception $e)
        {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error','Flag failed');
          throw $e;
        } catch (\Throwable $e)
        {
          $transaction->rollBack();
          throw $e;
        }
        return $this->renderAjax('claim');
    }

    /**
    * Autogenerate and display dynamic details target badge
    */
    public function actionBadge(int $id)
    {
      ob_get_clean();
      header('Content-Type: image/png');
      $target=$this->findModel($id);
      $fname=Yii::getAlias(sprintf('@app/web/images/targets/%s.png',$target->name));
      $src = imagecreatefrompng($fname);
      $skull = json_decode('"&#xf714;"');
      imagealphablending($src, false);
      imagesavealpha($src, true);
      $textcolor = imagecolorallocate($src, 255, 255, 255);
      $consolecolor = imagecolorallocate($src, 148,148,148);
      $greencolor = imagecolorallocate($src, 148,193,31);
      //imagettftext($src, 11.5, 0, 0, 14, $textcolor, Yii::getAlias('@app/web/webfonts/fa-solid-900.ttf'), $text);
      if(Headshot::find(['target_id'=>$target->id])->last()->one()) {
              $lastHeadshot=Headshot::find(['target_id'=>$target->id])->last()->one()->player->username;
      } else {
        $lastHeadshot="";
      }
      $lineheight=18;
      imagestring($src, 6, 40, $lineheight*3, sprintf("root@echoctf.red:/#",$target->name),$consolecolor);
      imagestring($src, 6, 215, $lineheight*3, sprintf("./target --stats %s",$target->name),$textcolor);
      imagestring($src, 6, 40, $lineheight*4, sprintf("ipv4.........: %s",long2ip($target->ip)),$greencolor);
      imagestring($src, 6, 40, $lineheight*5, sprintf("fqdn.........: %s",$target->fqdn),$greencolor);
      imagestring($src, 6, 40, $lineheight*6, sprintf("headshots....: %d",count($target->headshots)),$greencolor);
      imagestring($src, 6, 40, $lineheight*7, sprintf("last headshot: %s",$lastHeadshot),$greencolor);
      imagestring($src, 6, 40, $lineheight*8, sprintf("points.......: %s",number_format($target->points)),$greencolor);
      imagepng($src);
      imagedestroy($src);
      exit();

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

        throw new NotFoundHttpException('The requested target does not exist.');
    }
    protected function findProfile($id)
    {
        if (($model = \app\models\Profile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested profile does not exist.');
    }

}
