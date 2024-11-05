<?php

namespace app\modules\speedprogramming\controllers;

use Yii;
use app\modules\speedprogramming\models\SpeedSolution;
use app\modules\speedprogramming\models\SpeedSolutionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;
use Docker\API\Model\ExecIdJsonGetResponse200;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Stream\DockerRawStream;

/**
 * SpeedSolutionController implements the CRUD actions for SpeedSolution model.
 */
class DefaultController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ];
  }

  /**
   * Lists all SpeedSolution models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new SpeedSolutionSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single SpeedSolution model.
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
   * Creates a new SpeedSolution model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new SpeedSolution();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing SpeedSolution model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Submits a solution to container for validation
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionValidate($id)
  {
    $model = $this->findModel($id);
    try {
      $client = DockerClientFactory::create([
        //'remote_socket' => 'unix:///var/run/docker.sock',
        'remote_socket' => $model->server,
        'ssl' => false,
      ]);
      $docker = Docker::create($client);
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', 'Failed to connect to docker server.');
      return $this->redirect(['index']);
    }

    $restartPolicy = new RestartPolicy();
    $hostConfig = new HostConfig();
    $hostConfig->setMemory(512 * 1024 * 1024); // Set memory limit to 512MB
    $hostConfig->setRestartPolicy($restartPolicy);

    $containerConfig = new ContainersCreatePostBody();
    $endpointSettings = new EndpointSettings();
    $endpointIPAMConfig = new EndpointIPAMConfig();
    $endpointSettings->setIPAMConfig($endpointIPAMConfig);
    $containerConfig->setImage($model->validator_image); // target->image
    $containerConfig->setHostConfig($hostConfig);
    $containerConfig->setAttachStdin(true);
    $containerConfig->setAttachStdout(true);
    $containerConfig->setAttachStderr(true);
    $containerConfig->setCmd(['echo', 'I am running a command inside the validator']);
    $targetVariables[] = sprintf("FETCH_URL=https://" . \Yii::$app->sys->offense_domain . "/uploads/player_%d-target_%d.%s", $model->player_id, $model->problem_id);
    $targetVariables[] = sprintf("VALIDATE_LANG=%s", $model->language);
    $targetVariables[] = sprintf("VALIDATE_PLAYER=%s", $model->player_id);
    $targetVariables[] = sprintf("VALIDATE_PROBLEM=%s", $model->problem_id);
    $targetVariables[] = sprintf("VALIDATE_SUBMISSION=%s", $model->id);
    $containerConfig->setEnv($targetVariables); // target->targetVariables

    $containerCreateResult = $docker->containerCreate($containerConfig, ['name' => $model->language . 'validation_id' . $model->id]); // target->name
    $attachStream = $docker->containerAttach($containerCreateResult->getId(), [
      'stream' => true,
      'stdin' => true,
      'stdout' => true,
      'stderr' => true
    ]);
    $docker->containerStart($containerCreateResult->getId());
    $attachStream->onStdout(function ($stdout) use (&$model) {
      $model->modcomments .= date('Y-m-d H:i:s') . ' stdout: ';
      $model->modcomments .= $stdout;
    });
    $attachStream->onStderr(function ($stderr) use (&$model) {
      $model->modcomments .= date('Y-m-d H:i:s') . ' stderr: ';
      $model->modcomments .= $stderr;
    });

    $attachStream->wait();
    $docker->containerDelete($containerCreateResult->getId(), ['force' => true]);
    if ($model->save()) {
      Yii::$app->session->setFlash('success', 'Validation completed, check the modcomments for output.');
    } else
      Yii::$app->session->setFlash('error', 'Validation failed check the modcomments for output.');
    return $this->redirect(['view', 'id' => $model->id]);
  }

  /**
   * Deletes an existing SpeedSolution model.
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
   * Approves an existing SpeedSolution model.
   * If approval is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionApprove($id)
  {
    $model = $this->findModel($id);
    if ($model->approve()) {
      Yii::$app->session->setFlash('success', 'Submission approved.');
    } else {
      Yii::$app->session->setFlash('error', 'Failed to approve submission.');
    }

    return $this->redirect(['view', 'id' => $id]);
  }
  /**
   * Rejects an existing SpeedSolution model.
   * If rejection is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionReject($id)
  {
    $model = $this->findModel($id);
    if ($model->reject()) {
      Yii::$app->session->setFlash('success', 'Submission rejected succesfully.');
    } else {
      Yii::$app->session->setFlash('error', 'Failed to reject submission.');
    }
    return $this->redirect(['view', 'id' => $id]);
  }


  /**
   * Finds the SpeedSolution model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return SpeedSolution the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = SpeedSolution::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  private function getManager()
  {
    return self::getDocker();
  }

  public function testStartStream(): void
  {
    $createContainerResult = $this->createContainer();

    $execConfig = new ContainersIdExecPostBody();
    $execConfig->setAttachStdout(true);
    $execConfig->setAttachStderr(true);
    $execConfig->setCmd(['echo', 'output']);

    $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

    $execStartConfig = new ExecIdStartPostBody();
    $execStartConfig->setDetach(false);
    $execStartConfig->setTty(false);

    $stream = $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig);

    $this->assertInstanceOf(DockerRawStream::class, $stream);

    $stdoutFull = '';
    $stream->onStdout(function ($stdout) use (&$stdoutFull): void {
      $stdoutFull .= $stdout;
    });
    $stream->wait();

    $this->assertSame("output\n", $stdoutFull);

    self::getDocker()->containerKill($createContainerResult->getId(), [
      'signal' => 'SIGKILL',
    ]);
  }
  public function testExecFind(): void
  {
    $createContainerResult = $this->createContainer();

    $execConfig = new ContainersIdExecPostBody();
    $execConfig->setCmd(['/bin/true']);
    $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

    $execStartConfig = new ExecIdStartPostBody();
    $execStartConfig->setDetach(false);
    $execStartConfig->setTty(false);

    $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig);

    $execFindResult = $this->getManager()->execInspect($execCreateResult->getId());

    $this->assertInstanceOf(ExecIdJsonGetResponse200::class, $execFindResult);

    self::getDocker()->containerKill($createContainerResult->getId(), [
      'signal' => 'SIGKILL',
    ]);
  }

  private function createContainer()
  {
    $containerConfig = new ContainersCreatePostBody();
    $containerConfig->setImage('busybox:latest');
    $containerConfig->setCmd(['sh']);
    $containerConfig->setOpenStdin(true);
    $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

    $containerCreateResult = self::getDocker()->containerCreate($containerConfig);
    self::getDocker()->containerStart($containerCreateResult->getId());

    return $containerCreateResult;
  }
}
