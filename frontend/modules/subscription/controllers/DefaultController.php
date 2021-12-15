<?php
namespace app\modules\subscription\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use \app\modules\subscription\models\PlayerSubscription;
use \app\modules\subscription\models\Customer;
use \app\modules\subscription\models\Product;
use \app\modules\subscription\models\Subscription;

use \app\modules\subscription\models\InquiryForm;
/**
 * Default controller for the `subscription` module
 */
class DefaultController extends \app\components\BaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','success','checkout-session','redirect-customer-portal','customer-portal','create-checkout-session','webhook','inquiry'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create-checkout-session','customer-portal','redirect-customer-portal'],
                        'roles' => ['@'],
                        'verbs'=>['post'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','create-checkout-session','success','checkout-session','inquiry'],
                        'roles' => ['@'],
                    ],
                    [
                      'actions'=>['webhook'],
                      'allow'=>true,
                      'roles'=>['?'],
                      'verbs'=>['post'],
                      'ips' => [
                        //https://stripe.com/docs/ips
                        '3.18.12.63',
                        '3.130.192.231',
                        '13.235.14.237',
                        '13.235.122.149',
                        '35.154.171.200',
                        '52.15.183.38',
                        '54.187.174.169',
                        '54.187.205.235',
                        '54.187.216.72',
                        '54.241.31.99',
                        '54.241.31.102',
                        '54.241.34.107',
                        '127.0.0.1',
                      ],
                    ]
                ],
            ],
        ]);
    }


    public function actions()
    {
      $actions=parent::actions();
      $actions['webhook']['class']='app\modules\subscription\actions\WebhookRestAction';
      return $actions;
    }

    /**
     * Disable CSRF for webhook action
     */
    public function beforeAction($action) {
        if($action->id == 'webhook') {
            Yii::$app->request->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Renders the available subscription packages
     * @return string
     */
    public function actionIndex()
    {
        $mine=PlayerSubscription::findOne(\Yii::$app->user->id);
        $products=Product::find()->active()->orderBy(['weight'=>SORT_ASC,'name'=>SORT_ASC]);
        //if($mine && $mine->active)
        //{
        //  return $this->redirect(['/site/index']);
        //}
        $dataProvider=new ActiveDataProvider([
            'query' => $products,
            'pagination' => false,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'mine'=>$mine,
        ]);
    }

    /**
     * Verify Stripe sessionId and render a purchase success message.
     */
    public function actionSuccess($session_id)
    {
      try
      {
        \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
        $success_session = \Stripe\Checkout\Session::retrieve($session_id);
        return $this->render('success',[
          'success_session'=>$success_session
        ]);
      }
      catch(\Exception $e)
      {
        $this->redirect(['/subscription/default/index']);
      }
    }


    public function actionCheckoutSession($sessionId)
    {
      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
      \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
      $checkout_session = \Stripe\Checkout\Session::retrieve($sessionId);
      return $checkout_session->id;
    }

    public function actionCustomerPortal()
    {
      \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
      $sessionId = Yii::$app->request->getBodyParam('sessionId');

      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
      $checkout_session = \Stripe\Checkout\Session::retrieve($sessionId);
      $stripe_customer_id = $checkout_session->customer;

      $return_url = \yii\helpers\Url::toRoute('/subscription/default/index',true);
      try {
        $session = \Stripe\BillingPortal\Session::create([
          'customer' => $stripe_customer_id,
          'return_url' => $return_url,
        ]);

        return ['url' => $session->url];

      } catch (\Exception $e) {
        return [ 'url' => $return_url];
      }

    }
    /**
     * Action to redirect the user to its own customer portal, without
     * revealing any stripe related ID's
     * @return array ['url'=>'URL TO GO TO']
     */
    public function actionRedirectCustomerPortal()
    {
      \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

      $return_url = \yii\helpers\Url::toRoute('/profile/me',true);
      try
      {
        $session = \Stripe\BillingPortal\Session::create([
          'customer' => Yii::$app->user->identity->stripe_customer_id,
          'return_url' => $return_url,
        ]);
      }
      catch (\Exception $e)
      {
        return ['url'=>$return_url];
      }
      return ['url'=>$session->url];

    }
    /**
     * Create a stripe checkout session when a player clicks
     * the "sign up" button
     */
    public function actionCreateCheckoutSession()
    {
      \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
      $priceId=Yii::$app->request->post('priceId',null);
      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
      try {
        $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
        $cid=Customer::getCustomerId();
        $product=Product::findOne(['price_id'=>$priceId]);
        if($product===null)
        {
          throw new \Exception('No such price exist');
        }

        $mode='subscription';
        $line_items=[[
          'price' => $priceId,
          'quantity' => 1,
        ]];

        $checkout_session = \Stripe\Checkout\Session::create([
          'success_url' => \yii\helpers\Url::toRoute('/subscription/default/success',true).'?session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => \yii\helpers\Url::toRoute('/subscription/default/index',true),
          'payment_method_types' => ['card'],
          'mode' => $mode,
          'line_items' => $line_items,
          'customer'=>$cid,
          //'receipt_email'=>Yii::$app->user->identity->email,
          'metadata'=> ['player_id'=>Yii::$app->user->id,'profile_id'=>Yii::$app->user->identity->profile->id]
        ]);
      } catch (\Exception $e) {
        \Yii::$app->response->statusCode=418;
        if(empty($cid))
          \Yii::$app->response->statusCode=403;
        Yii::error($e->getMessage());
        return [
          'error' =>
          [
            'message' => $e->getMessage(),
          ],
        ];
      }
      return ['sessionId' => $checkout_session['id']];
    }

    public function actionInquiry()
    {
        $model = new InquiryForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            $model->defaults();
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }
}
