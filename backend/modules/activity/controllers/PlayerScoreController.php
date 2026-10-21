<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerScore;
use app\modules\activity\models\PlayerScoreSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerScoreController implements the CRUD actions for PlayerScore model.
 */
class PlayerScoreController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'rules' => [
        'class' => 'yii\filters\AjaxFilter',
        'only' => ['ajax-search']
      ],
      'access' => [
        'class' => \yii\filters\AccessControl::class,
        'rules' => [
          'adminActions' => [
            'allow' => true,
            'roles' => ['@'],
          ],
          'authActions' => [
            'allow' => true,
            'actions' => ['index', 'view', 'top15', 'export'],
            'roles' => ['@'],
          ],
          'denyAll' => [
            'allow' => false,
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ];
  }

  /**
   * Lists all PlayerScore models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PlayerScoreSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerScore model.
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
   * Creates a new PlayerScore model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new PlayerScore();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->player_id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing PlayerScore model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['index']);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing PlayerScore model.
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
   * Lists all TeamScore models.
   * @return mixed
   */
  public function actionTop15()
  {
    $searchModel = new PlayerScoreSearch();
    $params = Yii::$app->request->queryParams;
    $csv = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
    $academic_grouping = \Yii::$app->sys->academic_grouping ?: 1;

    for ($i = 0; $i < $academic_grouping; $i++) {
      $params['PlayerScoreSearch']['team_academic'] = $i;
      $dataProvider = $searchModel->search($params);
      $dataProvider->pagination->pageSize = 15;
      if (\Yii::$app->sys->academic_grouping !== false)
        $event_name = \Yii::$app->sys->{"event_name_" . $i};
      else
        $event_name = \Yii::$app->sys->event_name;
      fputcsv($csv, ['---------', $event_name, '---------']);
      fputcsv($csv, ['Rank', 'Username', 'Email', 'Points']);
      foreach ($dataProvider->getModels() as $k => $line) {
        fputcsv($csv, [$k + 1, $line->player->username, $line->player->email, $line->points]);
      }
    }
    rewind($csv);

    return \Yii::$app->response->sendStreamAsFile($csv, 'top15.csv');
  }

  /**
   * Lists all TeamScore models.
   * @return mixed
   */
  public function actionExport()
  {
    $searchModel = new PlayerScoreSearch();
    $params = Yii::$app->request->queryParams;
    $csv = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
    $academic_grouping = \Yii::$app->sys->academic_grouping ?: 1;
    for ($i = 0; $i < $academic_grouping; $i++) {
      $params['PlayerScoreSearch']['team_academic'] = $i;
      $dataProvider = $searchModel->search($params);
      $dataProvider->pagination = false;
      if (\Yii::$app->sys->academic_grouping !== false)
        $event_name = \Yii::$app->sys->{"event_name_" . $i};
      else
        $event_name = \Yii::$app->sys->event_name;
      fputcsv($csv, ['---------', $event_name, '---------']);
      fputcsv($csv, ['Rank', 'Username', 'Email', 'Points']);
      foreach ($dataProvider->getModels() as $k => $line) {
        fputcsv($csv, [$k + 1, $line->player->username, $line->player->email, $line->points]);
      }
    }
    rewind($csv);

    return \Yii::$app->response->sendStreamAsFile($csv, 'player_scores.csv');
  }

  /**
   * Finds the PlayerScore model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return PlayerScore the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PlayerScore::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
