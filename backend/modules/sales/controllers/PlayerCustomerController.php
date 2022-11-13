<?php

namespace app\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sales\models\PlayerCustomerSearch;
use app\modules\frontend\models\Player;
use app\modules\sales\models\PlayerSubscription;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `sales` module
 */
class PlayerCustomerController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
   public function behaviors()
   {
     return ArrayHelper::merge(parent::behaviors(),[]);
   }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
      $searchModel = new PlayerCustomerSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);
    }

    /**
     * Displays a single StripeWebhook model.
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
     * Fetch stripe customers and update their stripe_customer_id based on emails
     */
    public function actionFetchStripe()
    {
      PlayerCustomerSearch::FetchStripe();
      return $this->redirect(['index']);
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
        if (($model = Player::findOne($id)) === null)
        {
          throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        if (Yii::$app->request->isPost)
        {
            $postPlayer=Yii::$app->request->post('Player');
            $model->updateAttributes(['stripe_customer_id'=>$postPlayer['stripe_customer_id']]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StripeWebhook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->updateAttributes(['stripe_customer_id'=>null]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Player model based on its primary key and existance of stripe_customer_id value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Player the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Player::findOne($id);
        if ($model !== null && !empty($model->stripe_customer_id)) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}
