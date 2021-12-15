<?php

namespace app\modules\sales\controllers;

use Yii;
use app\modules\sales\models\ProductNetwork;
use app\modules\sales\models\ProductNetworkSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ProductNetworkController implements the CRUD actions for ProductNetwork model.
 */
class ProductNetworkController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
   public function behaviors()
   {
     return ArrayHelper::merge(parent::behaviors(),[]);
   }

    /**
     * Lists all ProductNetwork models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductNetworkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductNetwork model.
     * @param string $product_id
     * @param integer $network_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($product_id, $network_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($product_id, $network_id),
        ]);
    }

    /**
     * Creates a new ProductNetwork model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductNetwork();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'product_id' => $model->product_id, 'network_id' => $model->network_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductNetwork model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $product_id
     * @param integer $network_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($product_id, $network_id)
    {
        $model = $this->findModel($product_id, $network_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'product_id' => $model->product_id, 'network_id' => $model->network_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductNetwork model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $product_id
     * @param integer $network_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($product_id, $network_id)
    {
        $this->findModel($product_id, $network_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductNetwork model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $product_id
     * @param integer $network_id
     * @return ProductNetwork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($product_id, $network_id)
    {
        if (($model = ProductNetwork::findOne(['product_id' => $product_id, 'network_id' => $network_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
