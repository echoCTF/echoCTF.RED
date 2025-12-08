<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\TargetOndemand;
use app\modules\gameplay\models\TargetOndemandSearch;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * TargetOndemandController implements the CRUD actions for TargetOndemand model.
 */
class TargetOndemandController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all TargetOndemand models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new TargetOndemandSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Creates a new TargetOndemand model.
   * If creation is successful, the browser will be redirected to the 'index' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new TargetOndemand();
    try {
      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
      }
    } catch (\Exception $e) {
      if ($e->getCode() === '23000') {
        Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to add OnDemand entry for target. <b>A record for this target already existing</b>'));
      } else {
        Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to add OnDemand entry for target. <b>{exception}</b>', ['exception' => Html::encode($e->getMessage())]));
      }
      $model->updated_at = $model->created_at = '';
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing TargetOndemand model.
   * If update is successful, the browser will be redirected to the 'index' page.
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
   * Deletes an existing TargetOndemand model.
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
   * Clear an existing target state
   * If update is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionClear($id)
  {
    $model=$this->findModel($id);
    if ($model!==null && !$model->clear()) {
      \Yii::$app->getSession()->addFlash('error', Html::errorSummary($model));
    } else {
      \Yii::$app->getSession()->addFlash('success', 'Record synced!');
    }

    return $this->redirect(Yii::$app->request->referrer);
  }

  /**
   * Clear all target states for players
   * If successful, the browser will be redirected to the 'index' page.
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionClearAll()
  {
    $searchModel = new TargetOndemandSearch();
    $query = $searchModel->search(['TargetOndemandSearch' => Yii::$app->request->post()]);

    $query->pagination = false;

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = 0;
      foreach ($query->getModels() as $q) {
        if ($q->clear()) {
          $counter++;
        } else {
          $counter--;
        }
      }

      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] records synced', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::error($e->getMessage());
      Yii::$app->session->setFlash('error', 'Failed to sync records: ' . $e->getMessage());
    }
    return $this->redirect(Yii::$app->request->referrer);
  }

  /**
   * Finds the TargetOndemand model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return TargetOndemand the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = TargetOndemand::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
