<?php

namespace app\modules\sales\controllers;

use Yii;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\PlayerSubscriptionSearch;
use app\modules\sales\models\Product;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\NetworkPlayer;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * PlayerSubscriptionController implements the CRUD actions for PlayerSubscription model.
 */
class PlayerSubscriptionController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all PlayerSubscription models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PlayerSubscriptionSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerSubscription model.
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
   * Creates a new PlayerSubscription model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new PlayerSubscription();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->player_id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing PlayerSubscription model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      if ($model->active == 0) {
        $network_ids = ArrayHelper::getColumn($model->price->product->productNetworks, 'network_id');
        NetworkPlayer::deleteAll([
          'AND', 'player_id = :player_id', [
            'IN', 'network_id',
            $network_ids
          ]
        ], [
          ':player_id' => $model->player_id
        ]);
      }

      return $this->redirect(['view', 'id' => $model->player_id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing PlayerSubscription model.
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
   * Deletes all expired PlayerSubscription models.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDeleteInactive()
  {
    try {
      PlayerSubscription::DeleteInactive();
      Yii::$app->session->setFlash('success', 'Inactive subscriptions deleted.');
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', 'Failed to delete inactive subscriptions.[<code>' . \yii\helpers\Html::encode($e->getMessage()) . '</code>]');
    }
    return $this->redirect(['index']);
  }

  /**
   * Gets all Product from Stripe and merges with existing ones (if any).
   * @return mixed
   */
  public function actionFetchStripe()
  {
    if (intval(Product::find()->count()) < 1) {
      \Yii::$app->session->addFlash('warning', 'There are no products on the system. First fetch the stripe products and then import the subscriptions.');
      return $this->redirect(['/sales/product/index']);
    }
    if (intval(Player::find()->where(['IS NOT', 'stripe_customer_id', null])->count()) < 1) {
      \Yii::$app->session->addFlash('warning', 'There are no customers on the system. First fetch the stripe customers and then import the subscriptions.');
      return $this->redirect(['/sales/player-customer/index']);
    }
    PlayerSubscription::FetchStripe();
    return $this->redirect(['index']);
  }

  /**
   * Finds the PlayerSubscription model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return PlayerSubscription the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PlayerSubscription::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
