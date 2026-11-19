<?php

namespace app\modules\moderation\controllers;

use Yii;
use app\modules\moderation\models\SharedNetwork;
use app\modules\moderation\models\SharedNetworkSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SharedNetworkController implements the CRUD actions for SharedNetwork model.
 */
class SharedNetworkController extends \app\components\BaseController
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
          ],
        ],
      ]
    );
  }

  /**
   * Lists all SharedNetwork models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new SharedNetworkSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single SharedNetwork model.
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
   * Creates a new SharedNetwork model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new SharedNetwork();

    if ($this->request->isPost)
    {
      if ($model->load($this->request->post()) && $model->save())
      {
        Yii::$app->session->addFlash('success', Yii::t('app', 'Shared network {cidr} created', ['cidr' => $model->cidr]));
        return $this->redirect(['view', 'id' => $model->id]);
      }
    }
    else
    {
      $model->loadDefaultValues();
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing SharedNetwork model, the description only,
   * since the CIDR affects the containment checks.
   * @param int $id ID
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save())
    {
      Yii::$app->session->addFlash('success', Yii::t('app', 'Shared network {cidr} updated', ['cidr' => $model->cidr]));
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing SharedNetwork model.
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
   * Finds the SharedNetwork model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return SharedNetwork the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = SharedNetwork::findOne(['id' => $id])) !== null)
    {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
