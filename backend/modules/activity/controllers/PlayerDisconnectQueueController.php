<?php

namespace app\modules\activity\controllers;

use app\modules\activity\models\PlayerDisconnectQueue;
use app\modules\activity\models\PlayerDisconnectQueueSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerDisconnectQueueController implements the CRUD actions for PlayerDisconnectQueue model.
 */
class PlayerDisconnectQueueController extends BaseController
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
   * Lists all PlayerDisconnectQueue models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerDisconnectQueueSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerDisconnectQueue model.
   * @param int $player_id Player ID
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($player_id)
  {
    return $this->render('view', [
      'model' => $this->findModel($player_id),
    ]);
  }

  /**
   * Creates a new PlayerDisconnectQueue model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new PlayerDisconnectQueue();

    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'player_id' => $model->player_id]);
      }
    } else {
      $model->loadDefaultValues();
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing PlayerDisconnectQueue model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $player_id Player ID
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($player_id)
  {
    $model = $this->findModel($player_id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'player_id' => $model->player_id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing PlayerDisconnectQueue model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $player_id Player ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($player_id)
  {
    $this->findModel($player_id)->delete();

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
    PlayerDisconnectQueue::deleteAll();

    return $this->redirect(['index']);
  }

  /**
   * Finds the PlayerDisconnectQueue model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $player_id Player ID
   * @return PlayerDisconnectQueue the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($player_id)
  {
    if (($model = PlayerDisconnectQueue::findOne(['player_id' => $player_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
