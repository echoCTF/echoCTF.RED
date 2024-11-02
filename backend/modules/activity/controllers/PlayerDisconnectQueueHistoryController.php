<?php

namespace app\modules\activity\controllers;

use app\modules\activity\models\PlayerDisconnectQueueHistory;
use app\modules\activity\models\PlayerDisconnectQueueHistorySearch;
use app\components\BaseController;
use app\modules\activity\models\PlayerDisconnectQueue;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerDisconnectQueueHistoryController implements the CRUD actions for PlayerDisconnectQueueHistory model.
 */
class PlayerDisconnectQueueHistoryController extends BaseController
{
  /**
   * @inheritDoc
   */
  public function behaviors()
  {
    return array_merge(
      parent::behaviors(),
      [
        'verbs' => [
          'class' => VerbFilter::class,
          'actions' => [
            'delete' => ['POST'],
            'truncate' => ['POST'],
          ],
        ],
      ]
    );
  }

  /**
   * Lists all PlayerDisconnectQueueHistory models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerDisconnectQueueHistorySearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerDisconnectQueueHistory model.
   * @param int $id ID
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new PlayerDisconnectQueueHistory model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new PlayerDisconnectQueueHistory();

    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      }
    } else {
      $model->loadDefaultValues();
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing PlayerDisconnectQueueHistory model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id ID
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing PlayerDisconnectQueueHistory model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Deletes All existing PlayerDisconnectQueueHistory model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionTruncate()
  {
    PlayerDisconnectQueueHistory::deleteAll();

    return $this->redirect(['index']);
  }

  /**
   * Finds the PlayerDisconnectQueueHistory model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return PlayerDisconnectQueueHistory the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PlayerDisconnectQueueHistory::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
