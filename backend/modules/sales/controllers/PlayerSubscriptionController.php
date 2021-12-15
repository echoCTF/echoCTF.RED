<?php

namespace app\modules\sales\controllers;

use Yii;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\PlayerSubscriptionSearch;
use app\modules\sales\models\Product;
use app\modules\frontend\models\Player;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerSubscriptionController implements the CRUD actions for PlayerSubscription model.
 */
class PlayerSubscriptionController extends Controller
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
     * Gets all Product from Stripe and merges with existing ones (if any).
     * @return mixed
     */
    public function actionFetchStripe()
    {
      if(intval(Product::find()->count())<1)
      {
        \Yii::$app->session->addFlash('warning','There are no products on the system. First fetch the stripe products and then import the subscriptions.');
        return $this->redirect(['/sales/product/index']);
      }
      if(intval(Player::find()->where(['IS NOT', 'stripe_customer_id', null])->count())<1)
      {
        \Yii::$app->session->addFlash('warning','There are no customers on the system. First fetch the stripe customers and then import the subscriptions.');
        return $this->redirect(['/sales/player-customer/index']);
      }

      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $stripeSubs=$stripe->subscriptions->all([]);
      foreach($stripeSubs->data as $stripe_subscription)
      {
        $player=Player::findOne(['stripe_customer_id'=>$stripe_subscription->customer]);
        if($player!==null)
        {
          if(($ps=PlayerSubscription::findOne($player->id))===null)
          {
            $ps=new PlayerSubscription;
            $ps->player_id=$player->id;
          }
          $ps->subscription_id=$stripe_subscription->id;
          $ps->starting=new \yii\db\Expression("FROM_UNIXTIME(:starting)",[':starting'=>$stripe_subscription->current_period_start]);
          $ps->ending=new \yii\db\Expression("FROM_UNIXTIME(:ending)",[':ending'=>$stripe_subscription->current_period_end]);
          $ps->created_at=new \yii\db\Expression("FROM_UNIXTIME(:ts)",[':ts'=>$stripe_subscription->created]);
          $ps->updated_at=new \yii\db\Expression('NOW()');
          $ps->price_id=$stripe_subscription->items->data[0]->plan->id;
          $ps->active=intval($stripe_subscription->items->data[0]->plan->active);
          if(!$ps->save())
          {
            \Yii::$app->session->addFlash('error', sprintf('Failed to save subscription: %s',$stripe_subscription->id));
          }
          else
          {
            $ps->refresh();
            $sql="INSERT IGNORE INTO network_player (network_id,player_id,created_at,updated_at) SELECT network_id,:player_id,now(),now() FROM product_network WHERE product_id=:product_id";
            \Yii::$app->db->createCommand($sql)
            ->bindValue(':player_id',$player->id)
            ->bindValue(':product_id',$ps->product->id)
            ->execute();
            $metadata=json_decode($ps->product->metadata);
            if(isset($metadata->spins) && intval($metadata->spins)>0)
            {
              $player->playerSpin->updateAttributes(['perday'=>intval($metadata->spins),'counter'=>0]);
            }
            else
            {
              $player->playerSpin->updateAttributes(['counter'=>0]);
            }
            \Yii::$app->session->addFlash('success', sprintf('Imported subscription: %s for player %s',$stripe_subscription->id,$player->username));
          }
        }
      }
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
