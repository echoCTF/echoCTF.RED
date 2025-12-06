<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\TargetPlayerState;
use app\modules\activity\models\TargetPlayerStateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * TargetPlayerStateController implements the CRUD actions for TargetPlayerState model.
 */
class TargetPlayerStateController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all TargetPlayerState models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new TargetPlayerStateSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Creates a new TargetPlayerState model.
   * If creation is successful, the browser will be redirected to the 'index' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new TargetPlayerState();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['index']);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing TargetPlayerState model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @param integer $player_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id, $player_id)
  {
    $model = $this->findModel($id, $player_id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['index']);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing TargetPlayerState model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @param integer $player_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id, $player_id)
  {
    $this->findModel($id, $player_id)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Syncs an existing target state for a player
   * If update is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @param integer $player_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionSync($id, $player_id)
  {
    if (!$this->findModel($id, $player_id)->sync()) {
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
    $searchModel = new TargetPlayerStateSearch();
    $query = $searchModel->search(['TargetPlayerStateSearch' => Yii::$app->request->post()]);

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
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to sync records'));
    }
    return $this->redirect(['index']);
  }


  /**
   * Finds the TargetPlayerState model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @param integer $player_id
   * @return TargetPlayerState the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id, $player_id)
  {
    if (($model = TargetPlayerState::findOne(['id' => $id, 'player_id' => $player_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
