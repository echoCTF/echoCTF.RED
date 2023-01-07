<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Notification;
use app\modules\activity\models\NotificationSearch;
use app\modules\frontend\models\Player;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all Notification models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new NotificationSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Notification model.
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
   * Creates a new Notification model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {

    $model = new Notification();
    if ($model->load(Yii::$app->request->post())) {
      if ($model->player_id == null) {
        $err = false;
        $connection=\Yii::$app->db;
        $transaction = $connection->beginTransaction();

        // Send notification to all players
        try {
          foreach (Player::find()->where(['active' => 1, 'status' => 10])->all() as $player) {
            $notif = new Notification;
            $notif->load(Yii::$app->request->post());
            $notif->player_id = $player->id;
            if (!$notif->save()) {
              \Yii::$app->getSession()->addFlash('error', $notif->getErrorSummary(true));
              $err = true;
            }
          }
          $transaction->commit();
          if (!$err) {
            \Yii::$app->getSession()->addFlash('success', "Notifications send!");
            return $this->redirect(['index']);
          }
        } catch (\Exception $e) {
          $transaction->rollBack();
          \Yii::$app->getSession()->addFlash('error', $e->getMessage());
        }
      } else if ($model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      } else {
        \Yii::$app->getSession()->addFlash('error', $model->getErrorSummary(true));
      }
    }
    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Notification model.
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
   * Deletes an existing Notification model.
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
   * Finds the Notification model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Notification the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Notification::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
