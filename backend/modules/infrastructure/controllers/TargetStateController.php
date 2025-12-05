<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\infrastructure\models\TargetState;
use app\modules\infrastructure\models\TargetStateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
/**
 * TargetStateController implements the CRUD actions for TargetState model.
 */
class TargetStateController extends \app\components\BaseController
{
    /**
     * Lists all TargetState models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TargetStateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TargetState model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TargetState();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetState model.
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
     * Deletes an existing TargetState model.
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
   * Syncs an existing target state
   * If update is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionSync($id)
  {
    $model=$this->findModel($id);
    if ($model!==null && !$model->sync()) {
      \Yii::$app->getSession()->addFlash('error', Html::errorSummary($model));
    } else
      \Yii::$app->getSession()->addFlash('success', "Record synced!");

    return $this->redirect(['index']);
  }

  /**
   * Syncs all target states for players
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionSyncAll()
  {
    $searchModel = new TargetStateSearch();
    $query = $searchModel->search(['TargetStateSearch' => Yii::$app->request->post()]);

    $query->pagination = false;

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = 0;
      foreach ($query->getModels() as $q) {
        if ($q->sync()) $counter++;
        else $counter--;
      }

      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] records synced', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::error($e->getMessage());
      Yii::$app->session->setFlash('error', 'Failed to sync records: '.$e->getMessage());
    }
    return $this->redirect(['index']);
  }


    /**
     * Finds the TargetState model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TargetState the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TargetState::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
