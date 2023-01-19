<?php

namespace app\modules\sales\controllers;

use Yii;
use app\modules\sales\models\Product;
use app\modules\sales\models\ProductSearch;
use app\modules\sales\models\ProductNetwork;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
   public function behaviors()
   {
     return ArrayHelper::merge(parent::behaviors(),[]);
   }

    /**
     * Gets all Product from Stripe and syncs with current ones.
     * @return mixed
     */
    public function actionFetchStripe()
    {
      Product::FetchStripe();
      return $this->redirect(['index']);

    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param string $id
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAjaxSearch($term,$load=false,$active=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results=[];
        if (Yii::$app->request->isAjax)
        {
          $pq=Product::find()->select(['id','name','shortcode','active'])->where(['=','id',$term]);
          if($active!==null)
          {
            $pq->andWhere(['active'=>$active]);
          }
          if($load===false)
          {
            $pq->orWhere(['like','id',$term]);
          }
          $results=array_values(ArrayHelper::map($pq->all(),'id',
            function($model){
              return [
                'id'=>$model->id,
                'label'=>sprintf("(id: %s/%s) %s",$model->id,$model->shortcode,$model->name),
              ];
            }
          ));

        }
        return $results;
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
