<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\TargetSearch;
use app\modules\infrastructure\models\TargetExecCommandForm;
use app\modules\infrastructure\models\TargetInstanceSearch;
use app\modules\infrastructure\models\NetworkTargetScheduleSearch;

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
use yii\helpers\Json;
use app\modules\activity\models\HeadshotSearch;
use app\modules\activity\models\WriteupSearch;
use app\modules\activity\models\PlayerTargetHelpSearch;

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
    return ArrayHelper::merge(parent::behaviors(), [
      'rules' => [
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
    $searchModel = new TargetSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }
  public function actionDockerCompose()
  {
    $searchModel = new TargetSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination = false;
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
        'attributes' => ['name', 'difficulty', 'startedBy', 'solvedBy', 'solvedPct', 'fastestSolve', 'avgSolve'],
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
   * Displays full details about a target on a single page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionFullView($id)
  {
    $model = $this->findModel($id);
    return $this->render('full-view', [
      'model' => $model,
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
    $target = $this->findModel($id);
    try {
      $docker = $target->connectAPI();
      $webSocketStream = $docker->containerAttachWebsocket($target->name, [
        'stream' => true,
        'logs'   => true
      ]);
      $line = "";
      while ($line !== false && $line !== null) {
        $line = $webSocketStream->read();
        $lines[] = $line;
      }
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', Yii::t('app', "Failed to fetch logs. <b>{exception}</b>", ['exception' => Html::encode($e->getMessage())]));
      return $this->redirect(['view', 'id' => $target->id]);
    }
    return $this->render('logs', [
      'logs' => implode("", $lines),
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
    $target = $this->findModel($id);
    $form = new TargetExecCommandForm();
    $stdoutText = "";
    $stderrText = "";
    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
      try {
        $docker = $target->connectAPI();
        $execConfig = new ContainersIdExecPostBody();
        $execConfig->setTty($form->tty);
        $execConfig->setAttachStdout($form->stdout);
        $execConfig->setAttachStderr($form->stderr);

        $execConfig->setCmd($form->commandArray);
        $cexec = $docker->containerExec($target->name, $execConfig);
        $execid = $cexec->getId();
        $execStartConfig = new ExecIdStartPostBody();
        $execStartConfig->setDetach(false);

        // Execute the command
        $stream = $docker->execStart($execid, $execStartConfig);

        // To see the output stream of the 'exec' command
        $stream->onStdout(function ($stdout) use (&$stdoutText) {
          $stdoutText .= $stdout;
        });

        $stream->onStderr(function ($stderr) use (&$stderrText) {
          $stderrText .= $stderr;
        });

        $stream->wait();
      } catch (\Exception $e) {
        die(var_dump($e->getMessage()));
      }
    } else {
      $form->tty = true;
      $form->stdout = true;
    }
    return $this->render('exec', [
      'formModel' => $form,
      'stdout' => $stdoutText,
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
    $model = new Target();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
    $model = $this->findModel($id);
    $modelOrig = $this->findModel($id);
    $msg = Yii::t('app', "Server updated successfully");
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {

      // if the target has changed server destroy from old one
      if ($modelOrig->server != $model->server || array_key_exists('destroy', Yii::$app->request->post())) {
        $modelOrig->destroy();
        $msg = Yii::t('app', "Server destroyed and updated successfully");
      }
      if ($model->save()) {
        Yii::$app->session->setFlash('success', $msg);
        return $this->redirect(['view', 'id' => $model->id]);
      }
      Yii::$app->session->setFlash('error', Yii::t('app', 'Server failed to be updated [{errors}]', ['errors' => Html::encode(implode(", ", $model->errors))]));
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
    $searchModel = new TargetSearch();
    $query = $searchModel->search(['TargetSearch' => Yii::$app->request->post()]);
    $query->pagination = false;
    if (intval($query->count) === intval(Target::find()->count())) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You have attempted to delete all the records.'));
      return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
      //return $this->redirect(['index']);
    }

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->delete();
      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Targets deleted', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete targets'));
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
    $searchModel = new TargetSearch();
    $query = $searchModel->search(['TargetSearch' => Yii::$app->request->post()]);
    $query->pagination = false;
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->pull();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Target images pulled', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to pull targets'));
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
    $searchModel = new TargetSearch();
    $query = $searchModel->search(['TargetSearch' => Yii::$app->request->post()]);
    $query->pagination = false;
    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->active = 1;
      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Targets activated', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to activate targets'));
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
    $searchModel = new TargetSearch();
    $query = $searchModel->search(['TargetSearch' => Yii::$app->request->post()]);
    $query->pagination = false;
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->spin();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Targets spun', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to spin targets'));
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
    $model = $this->findModel($id);
    if ($model->destroy())
      Yii::$app->session->setFlash('success', Yii::t('app', 'Container destroyed from docker server [<code>{server}</code>]', ['server' => Html::encode($model->server)]));
    else
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to destroy container from docker server [<code>{server}</code>]', ['server' => Html::encode($model->server)]));

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
    try {
      if ($id === 'all') {
        $models = Target::find()->all();
        foreach ($models as $model)
          $model->spin();
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Containers successfully restarted'));
      } else {
        $this->findModel($id)->spin();
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Container successfully restarted'));
      }
    } catch (\Exception $e) {
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to restart container. {exception}', ['exception' => Html::encode($e->getMessage())]));
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
    if ($id === 'all') {
      $models = Target::find()->all();
      foreach ($models as $model)
        $model->pull();
      \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Images pulled successfully'));
      return $this->redirect(['index']);
    } else {
      if ($this->findModel($id)->pull())
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Image successfully pulled'));
      else {
        \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to pull container image'));
      }
      return $this->goBack(Yii::$app->request->referrer);
    }
  }

  /**
   * Return players progress for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionPlayerProgress($id)
  {
    $target = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\TargetPlayerStateSearch();
    $searchModel->id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('full-view/_player_progress', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return headshots for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionHeadshots($id)
  {
    $target = $this->findModel($id);
    $searchModel = new HeadshotSearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('full-view/_headshots', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return Instances for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionInstances($id)
  {
    $target = $this->findModel($id);
    $searchModel = new TargetInstanceSearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('full-view/_target_instances-tab', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

    /**
   * Return Instances for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionNetworkSchedule($id)
  {
    $target = $this->findModel($id);
    $searchModel = new NetworkTargetScheduleSearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('full-view/_network-schedule-tab', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return submitted writeups for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionWriteups($id)
  {
    $target = $this->findModel($id);
    $searchModel = new WriteupSearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('full-view/_writeups', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return Activated Writeups for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionActivatedWriteups($id)
  {
    $target = $this->findModel($id);
    $searchModel = new PlayerTargetHelpSearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->setSort([
      'defaultOrder' => ['created_at' => SORT_DESC, 'target_id' => SORT_ASC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
          'target_name' => [
            'asc' => ['target.name' => SORT_ASC],
            'desc' => ['target.name' => SORT_DESC],
          ],
          'created_at' => [
            'asc' =>  ['player_target_help.created_at' => SORT_ASC],
            'desc' => ['player_target_help.created_at' => SORT_DESC],
          ],
        ]
      ),
    ]);
    return Json::encode(trim($this->renderAjax('full-view/_activated_writeups', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return Spin history for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionSpinHistory($id)
  {
    $target = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\SpinHistorySearch();
    $searchModel->target_id = $target->id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

    return Json::encode(trim($this->renderAjax('full-view/_spin_history', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
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
    if (($model = Target::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  /*
     * Return an array of docker container statuses or null
     */
  private function docker_statuses()
  {
    $containers = [];
    foreach (Target::find()->select(['server'])->distinct()->all() as $target) {
      if ($target->server[0] === '/')
        $client = DockerClientFactory::create([
          'ssl' => false,
        ]);
      else
        $client = DockerClientFactory::create([
          'remote_socket' => $target->server,
          'ssl' => false,
        ]);

      try {
        $docker = Docker::create($client);
        $tmp = $docker->containerList(['all' => true]);
      } catch (\Exception $e) {
        continue;
      }
      $containers = array_merge($containers, $tmp);
    }
    return $containers;
  }

  public function actionAjaxSearch($term, $load = false)
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $results = [];
    if (Yii::$app->request->isAjax) {
      $pq = Target::find()->select(['id', 'name', 'ip']);
      if ($load === false) {
        $pq->where(['like', 'name', $term . '%', false]);
        $pq->orWhere(['LIKE', 'INET_NTOA(ip)', $term . '%', false]);
      } else {
        $pq->where(['=', 'id', $term]);
      }
      $results = array_values(ArrayHelper::map(
        $pq->all(),
        'id',
        function ($model) {
          return [
            'id' => $model->id,
            'label' => sprintf("(id: %d / %s) %s%s", $model->id, $model->ipoctet, $model->name, ($model->network !== null ? " [{$model->network->name}]" : "")),
          ];
        }
      ));
    }
    return $results;
  }
}
