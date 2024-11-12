<?php

namespace app\modules\speedprogramming\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\Stream;
use app\modules\speedprogramming\models\SpeedForm;
use app\modules\speedprogramming\models\SpeedSolution;
use app\modules\speedprogramming\models\SpeedProblem;

/**
 * Default controller for the `target` module
 */
class DefaultController extends \app\components\BaseController
{

  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['index', 'speed', 'view',],
        'rules' => [
          'eventEnd' => [
            'actions' => ['speed'],
          ],
          'eventStart' => [
            'actions' => ['speed'],
          ],

          'eventStartEnd' => [
            'actions' => ['index', 'view', 'speed'],
          ],
          'teamsAccess' => [
            'actions' => ['index', 'speed'],
          ],
          'disabledRoute' => [
            'actions' => ['view', 'index', 'speed'],
          ],
          [
            'allow' => false,
            'matchCallback'=>function($event){
              if(Yii::$app->sys->module_speedprogramming_enabled===true) {
                return false;
              }
              return true;
            }
          ],
          [
            'allow' => true,
            'actions' => ['speed'],
            'roles' => ['@'],
            'verbs' => ['post'],
          ],
          [
            'actions' => ['index'],
            'allow' => true,
            'roles' => ['@']
          ],
          [
            'actions' => ['view'],
            'allow' => true,
          ],
        ],
      ],
    ]);
  }


  /**
   * Renders a Target model details view
   * @return string
   */
  public function actionIndex()
  {
    $dataProvider = new ActiveDataProvider([
      'query' => SpeedProblem::find(),
      'pagination' => [
        'pageSizeParam' => 'speed-perpage',
        'pageParam' => 'speed-page',
      ]
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider
    ]);
  }


  /**
   * Renders a Target model details view
   * @return string
   */
  public function actionView(int $id)
  {
    $speed = $this->findModel($id);
    if (!$speed->active)
      $this->redirect(['/speed']);

    $speedForm = new SpeedForm();

    return $this->render('view', [
      'problem' => $speed,
      'speedForm' => $speedForm,
    ]);
  }

  /**
   * Finds the Target model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return SpeedProblem the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = \app\modules\speedprogramming\models\SpeedProblem::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested problem does not exist.');
  }

  protected function findProfile($id)
  {
    if (($model = \app\models\Profile::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested profile does not exist.');
  }



  public function actionAnswer($id)
  {
    $problem=$this->findModel($id);
    if (SpeedSolution::findOne(['player_id' => Yii::$app->user->id, 'problem_id' => $id]) !== null) {
      return $this->redirect(['/speedprogramming/default/index']);
    }

    $model = new SpeedForm();

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      $model->file = UploadedFile::getInstance($model, 'file');
      if ($model->file && $model->validate()) {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
          $model->file->saveAs('uploads/player_' . Yii::$app->user->id . '-target_' . $id . '.' . $model->language);
          $ss = new SpeedSolution();
          $ss->language = $model->language;
          $ss->player_id = Yii::$app->user->id;
          $ss->problem_id = $id;
          $ss->sourcecode = file_get_contents('uploads/player_' . Yii::$app->user->id . '-target_' . $id . '.' . $model->language);
          $ss->status = 'pending';
          $stream = new Stream();
          $stream->player_id = Yii::$app->user->id;
          $stream->model = 'solution';
          $stream->model_id = $id;
          $stream->points = 0;
          $stream->title = 'Submitted a solution for <code>' . $problem->name . '</code> for approval';
          $stream->message = $stream->pubmessage = $stream->pubtitle = $stream->title;
          if ($ss->save() && $stream->save(false))
            $transaction->commit();
          Yii::$app->session->setFlash('success', 'Submission accepted, please wait while our judges look into it.');
          return $this->redirect(['/speedprogramming/default/view', 'id' => $id]);
        } catch (\Exception $e) {
          $transaction->rollback();
          die(var_dump($e->getMessage()));
          Yii::$app->session->setFlash('error', 'Failed to accept your submission.');
          return $this->redirect(['/speedprogramming/default/view', 'id' => $id]);
        }
      }
    }
    return $this->render('_speed_form', ['model' => $model]);
  }
}
