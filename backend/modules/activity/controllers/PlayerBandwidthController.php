<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerBandwidth;
use app\modules\activity\models\PlayerBandwidthSearch;
use app\components\BaseController;
use Throwable;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerBandwidthController implements the CRUD actions for PlayerBandwidth model.
 */
class PlayerBandwidthController extends BaseController
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
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['POST'],
            'truncate' => ['POST'],
            'delete-filtered' => ['POST'],
          ],
        ],
      ]
    );
  }

  /**
   * Lists all PlayerBandwidth models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerBandwidthSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerBandwidth model.
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
   * Creates a new PlayerBandwidth model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new PlayerBandwidth();

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
   * Updates an existing PlayerBandwidth model.
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
   * Deletes existing PlayerBandwidth models based on grid filter.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @return \yii\web\Response
   * @throws Exception if the deletion fails
   */
  public function actionDeleteFiltered()
  {
    $searchModel = new PlayerBandwidthSearch();
    $query = $searchModel->search(['PlayerBandwidthSearch' => Yii::$app->request->post()]);
    $query->pagination = false;

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->delete();
      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Player Bandwidth records deleted', ['counter' => intval($counter)]));
    } catch (\Throwable $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete player bandwidth records'));
    }
    return $this->redirect(['index']);
  }

  /**
   * Deletes an existing PlayerBandwidth model.
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
   * Deletes the PlayerBandwidth model table.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionTruncate()
  {
    try {
      Yii::$app->db->createCommand()->truncateTable('player_bandwidth')->execute();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Player Bandwidth truncated'));
    } catch (\Throwable $e) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to truncate Player Bandwidth'));
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the PlayerBandwidth model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return PlayerBandwidth the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PlayerBandwidth::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
