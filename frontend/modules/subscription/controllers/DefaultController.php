<?php

namespace app\modules\subscription\controllers;

use Yii;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

use \app\modules\subscription\models\PlayerSubscription;
use \app\modules\subscription\models\PlayerPaymentHistory;
use \app\modules\subscription\models\PlayerProduct;
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
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['index', 'success', 'redirect-customer-portal', 'customer-portal', 'create-checkout-session', 'webhook', 'inquiry', 'cancel-subscription', 'payments', 'payment'],
        'rules' => [
          [
            'allow' => false,
            'actions' => ['index', 'success', 'redirect-customer-portal', 'customer-portal', 'create-checkout-session', 'cancel-subscription', 'payments', 'payment'],
            'matchCallback' => function () {
              return \Yii::$app->sys->subscriptions_emergency_suspend === true || \Yii::$app->sys->subscriptions_menu_show !== true;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is temporarily disabled, please try again in a couple of hours.'));
              return  \Yii::$app->getResponse()->redirect([\Yii::$app->sys->default_homepage]);
            }
          ],
          [
            'allow' => true,
            'actions' => ['create-checkout-session', 'customer-portal', 'redirect-customer-portal', 'cancel-subscription'],
            'roles' => ['@'],
            'verbs' => ['post'],
          ],
          [
            'allow' => true,
            'actions' => ['index', 'create-checkout-session', 'success', 'inquiry', 'payments', 'payment'],
            'roles' => ['@'],
          ],
          [
            'actions' => ['webhook'],
            'allow' => true,
            'roles' => ['?'],
            'verbs' => ['post'],
            'ips' => explode(",", Yii::$app->sys->stripe_webhook_ips), // https://stripe.com/docs/ips
            'matchCallback' => function () {
              return Yii::$app->sys->stripe_webhook_ips !== false;
            },
          ],
          // Allow webhook if no stripe_webhook_ips exist
          [
            'actions' => ['webhook'],
            'allow' => true,
            'roles' => ['?'],
            'verbs' => ['post'],
            'matchCallback' => function () {
              return Yii::$app->sys->stripe_webhook_ips === false;
            },
          ]
        ],
      ],
    ]);
  }


  public function actions()
  {
    $actions = parent::actions();
    $actions['webhook']['class'] = 'app\modules\subscription\actions\WebhookRestAction';
    return $actions;
  }

  /**
   * Disable CSRF for webhook action
   */
  public function beforeAction($action)
  {
    if ($action->id == 'webhook') {
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
    $mine = PlayerSubscription::findOne(\Yii::$app->user->id);
    $myproducts = PlayerProduct::find()->mine();
    $subscriptions = Product::find()->listed()->recurring()->ordered();
    $products = Product::find()->purchasable()->onetime()->notOwned()->ordered();

    $myproductsProvider = new ActiveDataProvider([
      'query' => $myproducts,
      'pagination' => false,
    ]);

    $productsProvider = new ActiveDataProvider([
      'query' => $products,
      'pagination' => false,
    ]);

    $subscriptionsProvider = new ActiveDataProvider([
      'query' => $subscriptions,
      'pagination' => false,
    ]);

    return $this->render('index', [
      'subscriptionsProvider' => $subscriptionsProvider,
      'productsProvider' => $productsProvider,
      'myproductsProvider' => $myproductsProvider,
      'myproducts' => $myproducts,
      'mine' => $mine,
    ]);
  }

  /**
   * Verify Stripe sessionId and render a purchase success message.
   */
  public function actionSuccess($session_id)
  {
    try {
      \Stripe\Stripe::setEnableTelemetry(false);
      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
      $success_session = \Stripe\Checkout\Session::retrieve($session_id);
      if ($success_session->subscription !== NULL) {
        $ps = PlayerSubscription::findOne(['player_id' => \Yii::$app->user->id, 'subscription_id' => $success_session->subscription, 'active' => 1]);
        if (!$ps) {
          Yii::$app->session->addFlash('error', Yii::t('app', 'Subscription not found!'));
          return $this->redirect(['/subscription/default/index']);
        }
      } else if ($success_session->mode === 'payment' && $success_session->payment_status === 'paid') {
        $ps = Price::findOne(['id' => $success_session->metadata->price_id]);
        if ($success_session->customer !== \Yii::$app->user->identity->stripe_customer_id)
          throw new \Exception("User " . \Yii::$app->user->identity->stripe_customer_id . " tried to access another player's price: " . $success_session->customer);

        if (!$ps) {
          Yii::$app->session->addFlash('error', Yii::t('app', 'Price not found!'));
          return $this->redirect(['/subscription/default/index']);
        }
      }
      if (!isset($ps))
        throw new \Exception("Neither PlayerSubscription nor Price exists for the given successfull sessionID");
      return $this->render('success', [
        'success_session' => $success_session,
        'mine' => $ps
      ]);
    } catch (\Throwable $e) {
      Yii::error($e->getMessage());
      Yii::$app->session->addFlash('error', Yii::t('app', 'No such session!'));
      return $this->redirect(['/subscription/default/index']);
    }
  }

  /**
   * Generate customer portal stripe url
   */
  public function actionCustomerPortal()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $sessionId = Yii::$app->request->getBodyParam('sessionId');

    \Stripe\Stripe::setEnableTelemetry(false);
    \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
    $checkout_session = \Stripe\Checkout\Session::retrieve($sessionId);
    $stripe_customer_id = $checkout_session->customer;
    $return_url = \yii\helpers\Url::toRoute('/subscription/default/index', true);
    try {
      if ($stripe_customer_id !== \Yii::$app->user->identity->stripe_customer_id)
        throw new \Exception("User " . \Yii::$app->user->identity->stripe_customer_id . " tried to access another customer's checkout session: " . $stripe_customer_id);

      $session = \Stripe\BillingPortal\Session::create([
        'customer' => $stripe_customer_id,
        'return_url' => $return_url,
      ]);

      return ['url' => $session->url];
    } catch (\Throwable $e) {
      \Yii::error($e, 'app');
      return ['url' => $return_url];
    }
  }

  /**
   * Action to redirect the user to its own customer portal, without
   * revealing any stripe related ID's
   * @return array ['url'=>'URL TO GO TO']
   */
  public function actionRedirectCustomerPortal()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $return_url = \yii\helpers\Url::toRoute('/subscriptions', true);
    try {
      \Stripe\Stripe::setEnableTelemetry(false);
      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

      $session = \Stripe\BillingPortal\Session::create([
        'customer' => Yii::$app->user->identity->stripe_customer_id,
        'return_url' => $return_url,
      ]);
    } catch (\Throwable $e) {
      return ['url' => $return_url];
    }
    return ['url' => $session->url];
  }

  /**
   * Create a stripe checkout session when a player clicks
   * the "sign up" button
   */
  public function actionCreateCheckoutSession()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $priceId = Yii::$app->request->post('priceId', null);
    try {
      \Stripe\Stripe::setEnableTelemetry(false);
      \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
      $cid = Customer::getCustomerId();
      $price = Price::findOne(['id' => $priceId]);
      if ($price === null || $price->product === null || $price->active == false || $price->product->active == false)
        throw new \Exception(\Yii::t('app', 'No such price or product exist'));

      $product = $price->product;

      if ($price->recurring_interval === 'one-off' || $price->recurring_interval === 'payment')
        $mode = 'payment';
      else
        $mode = 'subscription';

      if ($mode === 'subscription') {
        // One subscription per player, plan changes go through the customer portal
        foreach (\Stripe\Subscription::all(['customer' => $cid, 'status' => 'all', 'limit' => 100])->autoPagingIterator() as $sub) {
          if (!in_array($sub->status, ['canceled', 'incomplete_expired'], true)) {
            \Yii::$app->response->statusCode = 403;
            return ['error' => ['message' => \Yii::t('app', 'You already have a subscription, use Manage Billing to change it.')]];
          }
        }
        // Expire other open subscription checkouts so only one can complete
        foreach (\Stripe\Checkout\Session::all(['customer' => $cid, 'status' => 'open', 'limit' => 100])->autoPagingIterator() as $open) {
          if ($open->mode === 'subscription') {
            $open->expire();
          }
        }
      }

      $line_items = [[
        'price' => $priceId,
        'quantity' => 1,
      ]];

      $checkout_session = \Stripe\Checkout\Session::create([
        'success_url' => \yii\helpers\Url::toRoute('/subscription/default/success', true) . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => \yii\helpers\Url::toRoute('/subscription/default/index', true),
        'allow_promotion_codes' => true,
        'mode' => $mode,
        'line_items' => $line_items,
        'automatic_tax' => ['enabled' => \Yii::$app->sys->stripe_automatic_tax_enabled],
        'customer' => $cid,
        'metadata' => ['player_id' => Yii::$app->user->id, 'profile_id' => Yii::$app->user->identity->profile->id, 'price_id' => $priceId]
      ]);
    } catch (\Throwable $e) {
      \Yii::$app->response->statusCode = 403;
      if (empty($cid))
        \Yii::$app->response->statusCode = 403;
      Yii::error($e->getMessage());
      return [
        'error' => ['message' => \Yii::t('app', 'Unable to start the checkout, try again later.'),],
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
    $model = \app\modules\subscription\models\PlayerSubscription::findOne(\Yii::$app->user->id);
    if ($model !== null && $model->active) {
      try {
        \Stripe\Stripe::setEnableTelemetry(false);
        \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

        \Stripe\Subscription::update(
          $model->subscription_id,
          [
            'cancel_at_period_end' => true,
          ]
        );
        Yii::$app->session->setFlash('info', \Yii::t('app', 'Your subscription will be canceled at the end of the current billing period.'));
      } catch (\Throwable $e) {
        Yii::$app->session->setFlash('error', \Yii::t('app', 'There was an error canceling your subscription! Please contact our support.'));
      }
    } else {
      Yii::$app->session->setFlash('warning', \Yii::t('app', "You don't currently have an active subscription!"));
    }
    return $this->redirect(['/subscription/default/index']);
  }

  public function actionInquiry()
  {
    $model = new InquiryForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if ($model->sendInquiry()) {
        Yii::$app->session->setFlash('success', \Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
      } else {
        Yii::$app->session->setFlash('error', \Yii::t('app', 'There was an error sending email.'));
      }

      return $this->refresh();
    } else {
      $model->defaults();
      return $this->render('contact', [
        'model' => $model,
      ]);
    }
  }

  public function actionPayments()
  {
    $payments = PlayerPaymentHistory::find()->mine()->orderBy(['created_at' => SORT_DESC]);

    $paymentsProvider = new ActiveDataProvider([
      'query' => $payments,
      'pagination' => false,
    ]);
    return $this->render('payments', [
      'paymentsProvider' => $paymentsProvider,
    ]);
  }

  public function actionPayment($id)
  {
    $payment = PlayerPaymentHistory::findOne(['player_id' => \Yii::$app->user->id, 'payment_id' => $id]);
    if ($payment === null) {
      Yii::$app->session->setFlash('warning', \Yii::t('app', 'There is no such payment'));
    } else {
      $metadata = $payment->metadataObj;
      if ($metadata && isset($metadata->charges) && isset($metadata->charges->data)) {
        return $this->redirect($metadata->charges->data[0]->receipt_url);
      }
      // Newer PaymentIntents only have the latest charge id
      if ($metadata && !empty($metadata->latest_charge)) {
        try {
          \Stripe\Stripe::setEnableTelemetry(false);
          \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
          $charge = \Stripe\Charge::retrieve(is_string($metadata->latest_charge) ? $metadata->latest_charge : $metadata->latest_charge->id);
          if (!empty($charge->receipt_url)) {
            return $this->redirect($charge->receipt_url);
          }
        } catch (\Throwable $e) {
          Yii::error($e->getMessage());
        }
      }
      Yii::$app->session->setFlash('error', \Yii::t('app', 'No details found, contact support'));
    }
    return $this->redirect(['payments']);
  }
}
