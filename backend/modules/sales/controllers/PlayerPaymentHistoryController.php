<?php

namespace app\modules\sales\controllers;

use app\modules\sales\models\PlayerPaymentHistory;
use app\modules\sales\models\PlayerPaymentHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerPaymentHistoryController implements the CRUD actions for PlayerPaymentHistory model.
 */
class PlayerPaymentHistoryController extends \app\components\BaseController
{
  /**
   * @inheritDoc
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all PlayerPaymentHistory models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerPaymentHistorySearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerPaymentHistory model.
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
   * Creates a new PlayerPaymentHistory model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new PlayerPaymentHistory();

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
   * Updates an existing PlayerPaymentHistory model.
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
   * Deletes an existing PlayerPaymentHistory model.
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
   * Finds the PlayerPaymentHistory model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return PlayerPaymentHistory the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PlayerPaymentHistory::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
