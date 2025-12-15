<?php

namespace app\modules\activity\controllers;

use app\modules\activity\models\WsToken;
use app\modules\activity\models\WsTokenSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WsTokenController implements the CRUD actions for WsToken model.
 */
class WsTokenController extends BaseController
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
   * Lists all WsToken models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new WsTokenSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single WsToken model.
   * @param resource $token Token
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($token)
  {
    return $this->render('view', [
      'model' => $this->findModel($token),
    ]);
  }

  /**
   * Creates a new WsToken model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new WsToken();

    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'token' => $model->token]);
      }
    } else {
      $model->loadDefaultValues();
      $model->token = trim(str_replace(['_','+'], '-', \Yii::$app->security->generateRandomString(32)), '-');
      $model->expires_at=date('Y-m-d H:i:s', strtotime('+1 day'));
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing WsToken model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param resource $token Token
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($token)
  {
    $model = $this->findModel($token);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'token' => $model->token]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing WsToken model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param resource $token Token
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($token)
  {
    $this->findModel($token)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Finds the WsToken model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param resource $token Token
   * @return WsToken the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($token)
  {
    if (($model = WsToken::findOne(['token' => $token])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
