<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * PlayerController implements the CRUD actions for Player model.
 */
class PlayerController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'rules'=>[
              'class' => 'yii\filters\AjaxFilter',
              'only' => ['ajax-search']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'ban' => ['POST'],
                    'ban-filtered' => ['POST'],
                    'delete-filtered' => ['POST'],
                    'reset-playdata' => ['POST'],
                    'reset-authkey' => ['POST'],
                    'reset-player-progress' => ['POST'],
                    'toggle-academic' => ['POST'],
                    'toggle-active' => ['POST'],
                ],
            ],
        ]);
    }
    public function actions()
    {
      $actions=parent::actions();
      $actions['import']['class']='app\modules\frontend\actions\player\ImportAction';
      $actions['ban']['class']='app\modules\frontend\actions\player\BanAction';
      $actions['mail']['class']='app\modules\frontend\actions\player\MailAction';
      $actions['reset-player-progress']['class']='app\modules\frontend\actions\player\ResetPlayerProgressAction';
      $actions['reset-playdata']['class']='app\modules\frontend\actions\player\ResetPlaydataAction';
      return $actions;
    }

    /**
     * Lists all Player models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOvpn(int $id)
    {
      $model=$this->findModel($id);
      if($model->playerSsl===null)
      {
        \Yii::$app->session->addFlash('warning',\Yii::t('app',"No SSL record exists for this player."));
        return $this->goBack();
      }
      // bring the first available VPN client template
      $template=\app\modelscli\VpnTemplate::findOne(['active'=>true,'visible'=>true,'client'=>true]);
      $content=$this->renderPhpContent("?>".$template->content,['model'=>$model->playerSsl]);
      \Yii::$app->response->format=\yii\web\Response::FORMAT_RAW;
      \Yii::$app->response->content=$content;
      \Yii::$app->response->setDownloadHeaders($model->username.'.ovpn', 'application/octet-stream', false, strlen($content));
      \Yii::$app->response->send();
      return $this->goBack();
    }



    public function actionGraphs()
    {
        $dataProvider=Yii::$app->db->createCommand("select count(*) as registrations,count(if(active=1,1,0)) as activations,date(created) as dateindex from player group by date(created) ORDER BY date(created) ASC")->queryAll();
        $treasures=Yii::$app->db->createCommand("select count(*) as claims,date(ts) as dateindex from player_treasure group by date(ts) ORDER BY date(ts) ASC")->queryAll();
        $categories=[];
        $registrations=[];
        $activations=[];
        $claims=[];
        $dates=[];
        foreach($dataProvider as $key => $rec)
        {
          $categories[]=$rec['dateindex'];
          $registrations[]=intval(@$rec['registrations']);
          $activations[]=intval(@$rec['activations']);
        }
        foreach($treasures as $key => $rec)
        {
          $dates[]=$rec['dateindex'];
          $claims[]=intval(@$rec['claims']);
        }
        return $this->render('graphs', [
            'registrations' => $registrations,
            'categories'=>$categories,
            'claims'=>$claims,
            'claimDates'=>$dates
        ]);
    }

    /**
     * Displays a single Player model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Player model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Player();
        $trans=Yii::$app->db->beginTransaction();
        try
        {
          $model->scenario="create";
          if($model->load(Yii::$app->request->post()) && $model->save())
          {
              $playerSsl=new PlayerSsl();
              $playerSsl->player_id=$model->id;
              $playerSsl->generate();
              $playerSsl->save();
              $trans->commit();
              return $this->redirect(['view', 'id' => $model->id]);
          }
      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        \Yii::$app->getSession()->setFlash('error', 'Failed to create player. '.Html::encode($e->getMessage()));
      }
      return $this->render('create', [
          'model' => $model,
      ]);
    }


    /**
     * Updates an existing Player model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Player model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        if(($model=Player::findOne($id)) !== null && $model->delete() !== false)
          Yii::$app->session->setFlash('success', sprintf('Player [%s] deleted.', Html::encode($model->username)));
        else
          Yii::$app->session->setFlash('error', 'Player deletion failed.');
        return $this->redirect(['index']);
    }

    /**
     * Regenerates a player authKey
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionResetAuthkey($id)
    {
      $model=$this->findModel($id);
      $model->auth_key="";
      if($model->save())
      {
        Yii::$app->session->setFlash('success','Player auth_key regenerated');
      }
      else
      {
        Yii::$app->session->setFlash('error','Failed to reset player auth_key');
      }

      return $this->redirect(Yii::$app->request->referrer ?? ['index']);
    }

    /**
     * Toggles an existing Player academic flag model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleAcademic($id)
    {
      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $model=$this->findModel($id);
        $model->updateAttributes(['academic' => !$model->academic]);
        if($model->teamPlayer!==NULL)
        {
          $model->teamPlayer->delete();
        }

        if($model->team!==null)
        {
          $model->team->delete();
        }
        $trans->commit();
        Yii::$app->session->setFlash('success', 'User ['.Html::encode($model->username).'] academic set to '.Html::encode($model->academic));
      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to toggle academic flag for player');
      }

      return $this->redirect(Yii::$app->request->referrer ?? ['index']);
    }

    /**
     * Toggles an existing Player academic flag model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleActive($id)
    {
      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $model=$this->findModel($id);
        $model->updateAttributes(['active' => !$model->active]);
        $trans->commit();
        Yii::$app->session->setFlash('success', 'User ['.Html::encode($model->username).'] active set to '.Html::encode($model->active));
      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to toggle active flag for player');
      }

        return $this->redirect(['index']);
    }

    public function actionGenerateSsl($id) {
        $player=$this->findModel($id);
        $ps=$player->playerSsl;
        $ps->generate();
        if($ps->save())
        {
          Yii::$app->session->setFlash('success', "SSL Keys regenerated.");
          return $this->redirect(['/frontend/player/index']);
        }
        Yii::$app->session->setFlash('error', "Something went wrong with the SSL keys regeneration.");
        return $this->redirect(['/frontend/player/index']);
    }

    public function actionBanFiltered()
    {
      $searchModel=new PlayerSearch();
      $query=$searchModel->search(['PlayerSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      if(intval($query->count)===intval(Player::find()->count()))
      {
        Yii::$app->session->setFlash('error', 'Not allowed to ban all players. Please use the filters to limit the number of players to be banned.');
        return $this->redirect(['index']);
      }

      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $counter=$query->counter;
        foreach($query->getModels() as $q)
          $q->ban();

        $trans->commit();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Users banned');

      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to ban users');
      }
      return $this->redirect(['index']);
    }

    public function actionDeleteFiltered()
    {
      $searchModel=new PlayerSearch();
      $query=$searchModel->search(['PlayerSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      if(intval($query->count)===intval(Player::find()->count()))
      {
        Yii::$app->session->setFlash('error', 'You have attempted to delete all the records. Use the <b>Reset All player data</b> operation instead.');
        return $this->redirect(['index']);
      }

      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->delete();
        $trans->commit();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Users deleted');

      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to delete users');
      }
      return $this->redirect(['index']);
    }
    /**
     * Finds the Player model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Player the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if(($model=Player::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxSearch($term,$load=false,$active=null,$status=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results=[];
        if (Yii::$app->request->isAjax)
        {
          $pq=Player::find()->select(['id','username','email','status'])->where(['=','id',$term]);
          if($active!==null && $status!==null)
          {
            $pq->andWhere(['status'=>$status,'active'=>$active]);
          }
          if($load===false)
          {
            $pq->orWhere(['like','username',$term]);
          }
          $results=array_values(ArrayHelper::map($pq->all(),'id',
            function($model){
              return [
                'id'=>$model->id,
                'label'=>sprintf("(id: %d / pid: %d) %s <%s>%s",$model->id,$model->profile->id,$model->username,$model->email,$model->status===10 ? '':' (innactive)'),
              ];
            }
          ));

        }
        return $results;
    }

}
