<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\TargetSearch;
use app\modules\infrastructure\models\TargetExecCommandForm;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Docker\DockerClientFactory;
use Docker\Docker;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Http\Client\Socket\Exception\ConnectionException;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * TargetController implements the CRUD actions for Target model.
 */
class TargetController extends \app\components\BaseController
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
                    'destroy' => ['POST'],
                    'restart' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Target models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TargetSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionDockerCompose()
    {
        $searchModel=new TargetSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination=false;
        return $this->renderPartial('docker-compose', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Target Statuses.
     * @return mixed
     */
    public function actionStatus()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->docker_statuses(),
        ]);
        return $this->render('status', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Target Statistics.
     * @return mixed
     */
    public function actionStatistics()
    {
      $stats = Yii::$app->db->createCommand('select name,difficulty,target_started_count(id) as "startedBy",count(distinct t2.player_id) as "solvedBy", FORMAT(target_solved_percentage(id),2) as "solvedPct",min(t2.timer) as "fastestSolve",avg(t2.timer) as "avgSolve" FROM target as t1 left join headshot as t2 on t2.target_id=t1.id group by t1.id')
            ->queryAll();

      $dataProvider = new ArrayDataProvider([
          'allModels' => $stats,
          'sort' => [
            'attributes' => ['name', 'difficulty', 'startedBy','solvedBy','solvedPct','fastestSolve','avgSolve'],
          ],
      ]);
      return $this->render('stats', [
          'dataProvider' => $dataProvider,
      ]);
    }

    /**
     * Generate a single Target build files.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGenerate($id)
    {
        return $this->render('generate', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Displays a single Target model.
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
     * Displays a Target container logs.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLogs($id)
    {
        $target=$this->findModel($id);
        try {
          $docker=$target->connectAPI();
          $webSocketStream = $docker->containerAttachWebsocket($target->name, [
            'stream' => true,
            'logs'   => true
          ]);
          $line="";
          while($line!==false && $line!==null)
          {
            $line=$webSocketStream->read();
            $lines[]=$line;
          }
        }
        catch(\Exception $e)
        {
          Yii::$app->session->setFlash('error', "Failed to fetch logs. <b>".Html::encode($e->getMessage()).'</b>');
          return $this->redirect(['view','id'=>$target->id]);
        }
        return $this->render('logs', [
          'logs' => implode("",$lines),
          'model' => $target,
        ]);
    }

    /**
     * Executes a command on a running Target container.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionExec($id)
    {
        $target=$this->findModel($id);
        $form=new TargetExecCommandForm();
        $stdoutText = "";
        $stderrText = "";
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
          try {
            $docker=$target->connectAPI();
            $execConfig = new ContainersIdExecPostBody();
            $execConfig->setTty($form->tty);
            $execConfig->setAttachStdout($form->stdout);
            $execConfig->setAttachStderr($form->stderr);

            $execConfig->setCmd($form->commandArray);
            $cexec = $docker->containerExec($target->name,$execConfig);
            $execid = $cexec->getId();
            $execStartConfig = new ExecIdStartPostBody();
            $execStartConfig->setDetach(false);

            // Execute the command
            $stream = $docker->execStart($execid,$execStartConfig);

            // To see the output stream of the 'exec' command
            $stream->onStdout(function ($stdout) use (&$stdoutText) {
                $stdoutText .= $stdout;
            });

            $stream->onStderr(function ($stderr) use (&$stderrText) {
                $stderrText .= $stderr;
            });

            $stream->wait();
          }
          catch (\Exception $e)
          {
            die(var_dump($e->getMessage()));
          }
        }
        else {
          $form->tty=true;
          $form->stdout=true;
        }
        return $this->render('exec', [
          'formModel'=>$form,
          'stdout'=>$stdoutText,
          "stderr" => $stderrText,
          'model' => $target,
        ]);

    }

    /**
     * Creates a new Target model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Target();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Target model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);
        $modelOrig=$this->findModel($id);
        $msg="Server updated successfully";
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {

          // if the target has changed server destroy from old one
          if($modelOrig->server != $model->server || array_key_exists('destroy', Yii::$app->request->post()))
          {
            $modelOrig->destroy();
            $msg="Server destroyed and updated successfully";
          }
          if($model->save())
          {
            Yii::$app->session->setFlash('success', $msg);
            return $this->redirect(['view', 'id' => $model->id]);
          }
          Yii::$app->session->setFlash('error', 'Server failed to be updated ['.Html::encode(implode(", ", $model->errors)).']');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Target model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes Target model matching search criteria.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteFiltered()
    {
      $searchModel=new TargetSearch();
      $query=$searchModel->search(['TargetSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      if(intval($query->count)===intval(Target::find()->count()))
      {
        Yii::$app->session->setFlash('error', 'You have attempted to delete all the records.');
        return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
        //return $this->redirect(['index']);
      }

      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->delete();
        $trans->commit();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Targets deleted');

      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to delete targets');
      }
      return $this->redirect(['index']);
    }

    /**
     * Pulls Target images for models matching search criteria.
     * If pull is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPullFiltered()
    {
      $searchModel=new TargetSearch();
      $query=$searchModel->search(['TargetSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->pull();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Targets pulled');
      }
      catch(\Exception $e)
      {
        Yii::$app->session->setFlash('error', 'Failed to pull targets');
      }
      return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
    }

    /**
     * Deletes Target model matching search criteria.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivateFiltered()
    {
      $searchModel=new TargetSearch();
      $query=$searchModel->search(['TargetSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->active=1;
        $trans->commit();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Targets activated');

      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', 'Failed to activate targets');
      }
      return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
    }

    /**
     * Spins Targets for models matching search criteria.
     * If spin is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSpinFiltered()
    {
      $searchModel=new TargetSearch();
      $query=$searchModel->search(['TargetSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->spin();
        Yii::$app->session->setFlash('success', '[<code><b>'.intval($counter).'</b></code>] Targets spinned');
      }
      catch(\Exception $e)
      {
        Yii::$app->session->setFlash('error', 'Failed to spin targets');
      }
      return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
    }


    /**
     * Destroys the Container of an existing Target model.
     * If destruction is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDestroy($id)
    {
      $model=$this->findModel($id);
        if($model->destroy())
          Yii::$app->session->setFlash('success', 'Container destroyed from docker server [<code>'.Html::encode($model->server).'</code>]');
        else
          Yii::$app->session->setFlash('error', 'Failed to destroy container from docker server [<code>'.Html::encode($model->server).'</code>]');

        return $this->goBack(Yii::$app->request->referrer);
    }

    /**
     * Spin an existing Target model.
     * If spin is successful, the browser will be redirected to the 'index' page.
     * @param mixed $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSpin($id)
    {
      try
      {
        if($id === 'all')
        {
          $models=Target::find()->all();
          foreach($models as $model)
            $model->spin();
          \Yii::$app->getSession()->setFlash('success', 'Containers successfully restarted');
        }
        else
        {
          $this->findModel($id)->spin();
          \Yii::$app->getSession()->setFlash('success', 'Container successfully restarted');
        }
      }
      catch(\Exception $e)
      {
        \Yii::$app->getSession()->setFlash('error', 'Failed to restart container. '.Html::encode($e->getMessage()));
      }
      return $this->goBack(Yii::$app->request->referrer);
    }

    /**
     * Pull an image based of existing Target model.
     * If pull is successful, the browser will be redirected to the 'index' page.
     * @param mixed $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPull($id)
    {
        if($id === 'all')
        {
          $models=Target::find()->all();
          foreach($models as $model)
            $model->pull();
          \Yii::$app->getSession()->setFlash('success', 'Images pulled successfully');
          return $this->redirect(['index']);

        }
        else
        {
          if($this->findModel($id)->pull())
            \Yii::$app->getSession()->setFlash('success', 'Image successfully pulled');
          else
          {
            \Yii::$app->getSession()->setFlash('error', 'Failed to pull container image');
          }
          return $this->goBack(Yii::$app->request->referrer);
        }

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
        if(($model=Target::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
     * Return an array of docker container statuses or null
     */
    private function docker_statuses()
    {
      $containers=[];
      foreach(Target::find()->select(['server'])->distinct()->all() as $target)
      {
        if($target->server[0]==='/')
          $client=DockerClientFactory::create([
            'ssl' => false,
          ]);
        else
          $client=DockerClientFactory::create([
            'remote_socket' => $target->server,
            'ssl' => false,
          ]);

        try
        {
          $docker=Docker::create($client);
          $tmp=$docker->containerList(['all'=>true]);
        }
        catch(\Exception $e)
        {
          continue;
        }
        $containers=array_merge($containers,$tmp);

      }
      return $containers;
    }

    public function actionAjaxSearch($term,$load=false)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results=[];
        if (Yii::$app->request->isAjax)
        {
          $pq=Target::find()->select(['id','name','ip']);
          if($load===false)
          {
            $pq->where(['like','name',$term.'%',false]);
            $pq->orWhere(['LIKE','INET_NTOA(ip)',$term.'%',false]);
          }
          else
          {
            $pq->where(['=','id',$term]);
          }
          $results=array_values(ArrayHelper::map($pq->all(),'id',
            function($model){
              return [
                'id'=>$model->id,
                'label'=>sprintf("(id: %d / %s) %s%s",$model->id,$model->ipoctet,$model->name,($model->network!==null?" [{$model->network->name}]":"")),
              ];
            }
          ));

        }
        return $results;
    }

}
