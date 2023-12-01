<?php
namespace app\modules\subscription\controllers;

use Yii;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

use \app\modules\subscription\models\PlayerSubscription;
use \app\modules\subscription\models\Customer;
use \app\modules\subscription\models\Product;
use \app\modules\subscription\models\Price;
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
                'only' => ['index','success', 'redirect-customer-portal','customer-portal','create-checkout-session','webhook','inquiry', 'cancel-subscription'],
                'rules' => [
                    [
                      'allow' => false,
                      'actions'=>['index', 'success', 'redirect-customer-portal','customer-portal','create-checkout-session', 'cancel-subscription'],
                      'matchCallback' => function () {
                        return \Yii::$app->sys->subscriptions_emergency_suspend===true || \Yii::$app->sys->subscriptions_menu_show!==true;
                      },
                      'denyCallback' => function () {
                        Yii::$app->session->setFlash('info', \Yii::t('app','This area is temporarily disabled, please try again in a couple of hours.'));
                        return  \Yii::$app->getResponse()->redirect([\Yii::$app->sys->default_homepage]);
                      }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-checkout-session','customer-portal','redirect-customer-portal', 'cancel-subscription'],
                        'roles' => ['@'],
                        'verbs'=>['post'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','create-checkout-session','success', 'inquiry'],
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
                        '18.211.135.69',
                        '35.154.171.200',
                        '52.15.183.38',
                        '54.88.130.119',
                        '54.88.130.237',
                        '54.187.174.169',
                        '54.187.205.235',
                        '54.187.216.72',
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
        $products=Product::find()->purchasable()->ordered();

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
        $ps=PlayerSubscription::findOne(['player_id'=>\Yii::$app->user->id,'subscription_id'=>$success_session->subscription]);
        if(!$ps)
        {
          return $this->redirect(['/subscription/default/index']);
        }
        return $this->render('success',[
          'success_session'=>$success_session,
          'mine'=>$ps
        ]);
      }
      catch(\Exception $e)
      {
        return $this->redirect(['/subscription/default/index']);
      }
    }

    /**
     * Generate customer portal stripe url
     */
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
      $return_url = \yii\helpers\Url::toRoute('/profile/me',true);
      try
      {
        \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

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
      try {
        \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
        $cid=Customer::getCustomerId();
        $product=Price::findOne(['id'=>$priceId])->product;
        if($product===null)
        {
          throw new \Exception(\Yii::t('app','No such price exist'));
        }

        $mode='subscription';
        $line_items=[[
          'price' => $priceId,
          'quantity' => 1,
        ]];

        $checkout_session = \Stripe\Checkout\Session::create([
          'success_url' => \yii\helpers\Url::toRoute('/subscription/default/success',true).'?session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => \yii\helpers\Url::toRoute('/subscription/default/index',true),
          'allow_promotion_codes'=>true,
          'payment_method_types' => ['card'],
          'mode' => $mode,
          'line_items' => $line_items,
          'automatic_tax' => ['enabled' => \Yii::$app->sys->stripe_automatic_tax_enabled],
          'customer'=>$cid,
          'metadata'=> ['player_id'=>Yii::$app->user->id,'profile_id'=>Yii::$app->user->identity->profile->id]
        ]);
      }
      catch (\Exception $e)
      {
        \Yii::$app->response->statusCode=403;
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

    /**
     * Update a player subscription to be canceled at the end of its period.
     * https://stripe.com/docs/billing/subscriptions/cancel#reactivating-canceled-subscriptions
     */
    public function actionCancelSubscription()
    {
      $model=\app\modules\subscription\models\PlayerSubscription::findOne(\Yii::$app->user->id);
      if($model!==null && $model->active)
      {
        try
        {
          \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

          \Stripe\Subscription::update(
            $model->subscription_id,
            [
              'cancel_at_period_end' => true,
            ]
          );
          Yii::$app->session->setFlash('info', \Yii::t('app','Your subscription will be canceled at the end of the current billing period.'));

        }
        catch (\Exception $e)
        {
          Yii::$app->session->setFlash('error', \Yii::t('app','There was an error canceling your subscription! Please contact our support.'));
        }
      }
      else
      {
        Yii::$app->session->setFlash('warning', \Yii::t('app',"You don't currently have an active subscription!"));
      }
      return $this->redirect(['/subscription/default/index']);
    }

    public function actionInquiry()
    {
        $model = new InquiryForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendInquiry()) {
                Yii::$app->session->setFlash('success', \Yii::t('app','Thank you for contacting us. We will respond to you as soon as possible.'));
            } else {
                Yii::$app->session->setFlash('error', \Yii::t('app','There was an error sending email.'));
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
