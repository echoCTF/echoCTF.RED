<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\infrastructure\models\TargetInstance;
use app\modules\infrastructure\models\TargetInstanceSearch;
use app\modules\infrastructure\models\DockerContainer;
use app\modules\infrastructure\models\TargetExecCommandForm;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\base\UserException;


/**
 * TargetInstanceController implements the CRUD actions for TargetInstance model.
 */
class TargetInstanceController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'destroy' => ['POST'],
                    'restart' => ['POST'],
                ],
            ],
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
          if($docker===false)
            throw new UserException(Yii::t('app','Failed to connect to the docker API'));


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
          Yii::$app->session->setFlash('error', Yii::t('app',"Failed to fetch logs. <b>{exception}</b>",['exception'=>Html::encode($e->getMessage())]));
          return $this->redirect(['view','id'=>$target->player_id]);
        }
        return $this->render('logs', [
          'logs' => implode("",$lines),
          'model' => $target,
        ]);
    }

    /**
     * Executes a command on a running Target container.
     * @param integer $player_id
     * @param integer $target_id
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
            if($docker===false)
                throw new UserException(Yii::t('app',"Failed to connect to docker API"));
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
            Yii::$app->session->setFlash('error', Yii::t('app',"Failed to execute command. <b>{exception}</b>",['exception'=>Html::encode($e->getMessage())]));
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
     * Lists all TargetInstance models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(($ret=$this->prerequisitesCheck())!==[])
        {
            return $this->redirect($ret);
        }

        $searchModel = new TargetInstanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TargetInstance model.
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
     * Creates a new TargetInstance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(($ret=$this->prerequisitesCheck())!==[])
        {
            return $this->redirect($ret);
        }

        $model = new TargetInstance();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetInstance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Start a Target Instance .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestart($id)
    {
        try
        {
            $val=$this->findModel($id);
            $dc=new DockerContainer($val->target);
            $dc->targetVolumes=$val->target->targetVolumes;
            $dc->targetVariables=$val->target->targetVariables;
            $dc->name=$val->name;
            $dc->server=$val->server->connstr;
            try
            {
                $dc->destroy();
            }
            catch (\Exception $e) { }
            $dc->pull();
            $dc->spin();
            $val->ipoctet=$dc->container->getNetworkSettings()->getNetworks()->{$val->server->network}->getIPAddress();
            $val->reboot=0;
            $val->save();
        }
        catch (\Exception $e)
        {
            if(method_exists($e,'getErrorResponse'))
                echo $e->getErrorResponse()->getMessage(),"\n";
            else
                echo $e->getMessage(),"\n";
        }

        return $this->redirect(['index']);
    }

    /**
     * Destroy a Target Instance .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDestroy($id)
    {
        try
        {
            $val=$this->findModel($id);
            $dc=new DockerContainer($val->target);
            $dc->targetVolumes=$val->target->targetVolumes;
            $dc->targetVariables=$val->target->targetVariables;
            $dc->name=$val->name;
            $dc->server=$val->server->connstr;
            try
            {
                $dc->destroy();
            }
            catch (\Exception $e) { }
            $val->delete();
        }
        catch (\Exception $e)
        {
          if(method_exists($e,'getErrorResponse'))
            echo $e->getErrorResponse()->getMessage(),"\n";
          else
            echo $e->getMessage(),"\n";
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TargetInstance model.
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
     * Finds the TargetInstance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TargetInstance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TargetInstance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Check for prerequisite players, targets and servers
     * @param boolean $player Whether to check for players exist
     * @param boolean $target Whether to check for target exist
     * @param boolean $server Whether to check for server exist
     * @return array
     */
    protected function prerequisitesCheck($player=true,$target=true,$server=true)
    {
        if((bool) $player && (int)\app\modules\frontend\models\Player::find()->count() === 0)
        {
          Yii::$app->session->setFlash('warning', Yii::t('app',"No players found create one first."));
          return ['/frontend/player/create'];
        }

        if((bool) $target && (int)\app\modules\gameplay\models\Target::find()->count() === 0)
        {
          Yii::$app->session->setFlash('warning', Yii::t('app',"No targets found create one first."));
          return ['/gameplay/target/create'];
        }

        if((bool) $server && (int)\app\modules\infrastructure\models\Server::find()->count() === 0)
        {
          Yii::$app->session->setFlash('warning', Yii::t('app',"No servers found create one first."));
          return ['/infrastructure/server/create'];
        }
        return [];
    }
}
