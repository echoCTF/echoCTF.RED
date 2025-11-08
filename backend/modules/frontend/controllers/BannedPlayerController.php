<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\BannedPlayer;
use app\modules\frontend\models\BannedPlayerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * BannedPlayerController implements the CRUD actions for BannedPlayer model.
 */
class BannedPlayerController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all BannedPlayer models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new BannedPlayerSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $optimize = $searchModel::needsOptimization();
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'optimize' => $optimize,
    ]);
  }

  /**
   * Displays a single BannedPlayer model.
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
   * Creates a new BannedPlayer model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new BannedPlayer();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing BannedPlayer model.
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
   * Deletes an existing BannedPlayer model.
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
   * Optimises the existing BannedPlayer records.
   * Deletes emails based on wild cards.
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionOptimize()
  {
    try {
      $records = Yii::$app->db->createCommand("DELETE p1 FROM banned_player p1 JOIN banned_player p2   ON p1.email LIKE p2.email WHERE p1.id <> p2.id")->execute();

      if ($records > 0) {
        \Yii::$app->session->addFlash('success', "Deleted {$records} records.");
      } else {
        \Yii::$app->session->addFlash('warning', "No matching records found.");
      }
    } catch (\Exception $e) {
      \Yii::$app->session->addFlash('error', "Error: " . $e->getMessage());
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the BannedPlayer model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return BannedPlayer the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = BannedPlayer::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
