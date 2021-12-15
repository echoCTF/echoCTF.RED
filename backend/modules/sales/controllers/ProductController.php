<?php

namespace app\modules\sales\controllers;

use Yii;
use app\modules\sales\models\Product;
use app\modules\sales\models\ProductSearch;
use app\modules\sales\models\ProductNetwork;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Gets all Product from Stripe and syncs with current ones.
     * @return mixed
     */
    public function actionFetchStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $products=$stripe->products->all(['active'=>true]);
      foreach($products->data as $stripeProduct)
      {
        $product=Product::findOne($stripeProduct->id);

        if($product===null)
        {
          $product=new Product();
          $product->id=$stripeProduct->id;
          $product->created_at=new \yii\db\Expression('NOW()');
        }
        $product->active=$stripeProduct->active;
        $product->name=$stripeProduct->name;
        $product->description=$stripeProduct->description;
        $product->livemode=$stripeProduct->livemode;
        $prices=$stripe->prices->all(['product'=>$product->id]);
        $price=$prices->data[0];

        if(isset($price->recurring) && $price->recurring->interval)
          $product->interval=$price->recurring->interval;
        else
          $product->interval='day';

        if(isset($price->recurring) && $price->recurring->interval_count)
          $product->interval_count=$price->recurring->interval_count;
        else
          $product->interval_count=1;

        $product->price_id=$price->id;
        $product->currency=$price->currency;
        $product->metadata=json_encode($stripeProduct->metadata);
        $product->unit_amount=$price->unit_amount;
        $product->updated_at=new \yii\db\Expression('NOW()');
        if($stripeProduct->metadata->htmlOptions)
          $product->htmlOptions=trim($stripeProduct->metadata->htmlOptions);

        if($stripeProduct->metadata->shortcode)
          $product->shortcode=trim($stripeProduct->metadata->shortcode);

        if($stripeProduct->metadata->perks)
          $product->perks=trim($stripeProduct->metadata->perks);

        if($stripeProduct->metadata->weight)
          $product->weight=intval(trim($stripeProduct->metadata->weight));

        if(!$product->save())
        {
          die(var_dump($product));
          \Yii::$app->session->addFlash('error', sprintf('Failed to save product: %s, %s',$stripeProduct->id,$stripeProduct->name));
        }
        else
          \Yii::$app->session->addFlash('success', sprintf('Imported product: %s, %s',$stripeProduct->id,$stripeProduct->name));
        if(!empty($stripeProduct->metadata->network_ids))
        {
          foreach(explode(',',$stripeProduct->metadata->network_ids) as $nid)
          {
            if(ProductNetwork::findOne(['product_id'=>$product->id, 'network_id'=>$nid])===null)
            {
              $pn=new ProductNetwork;
              $pn->product_id=$product->id;
              $pn->network_id=$nid;
              $pn->save();
            }
          }
        }
      }
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
