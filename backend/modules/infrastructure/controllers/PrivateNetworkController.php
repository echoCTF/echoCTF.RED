<?php

namespace app\modules\infrastructure\controllers;

use app\modules\infrastructure\models\PrivateNetwork;
use app\modules\infrastructure\models\PrivateNetworkSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PrivateNetworkController implements the CRUD actions for PrivateNetwork model.
 */
class PrivateNetworkController extends BaseController
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
                  ],
              ],
          ]
      );
  }

    /**
     * Lists all PrivateNetwork models.
     *
     * @return string
     */
  public function actionIndex()
  {
      $searchModel = new PrivateNetworkSearch();
      $dataProvider = $searchModel->search($this->request->queryParams);

      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);
  }

    /**
     * Displays a single PrivateNetwork model.
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
     * Creates a new PrivateNetwork model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
  public function actionCreate()
  {
      $model = new PrivateNetwork();

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
     * Updates an existing PrivateNetwork model.
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
     * Deletes an existing PrivateNetwork model.
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
     * Finds the PrivateNetwork model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PrivateNetwork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
  protected function findModel($id)
  {
    if (($model = PrivateNetwork::findOne(['id' => $id])) !== null) {
        return $model;
    }

      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
