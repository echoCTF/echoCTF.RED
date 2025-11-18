<?php
/* @var $this yii\web\View */

use \yii\helpers\Html;
use yii\widgets\ListView;

$subscription = Yii::$app->getModule('subscription');

?>
<div class="container">
  <?=ListView::widget([
    'id' => 'subscriptionBased',
    'dataProvider' => $subscriptionsProvider,
    'emptyText' => '<p class="text-info"><b>' . \Yii::t('app', 'There are no subscriptions available at the moment...') . '</b></p>',
    'options' => ['class' => 'list-view row d-flex justify-content-center'],
    'summary' => false,
    'itemOptions' => [
      'tag' => false,
    ],
    'itemView' => '_subscription',
    'viewParams' => ['mine' => $mine],
  ]);?><!--//subscriptionBased-->
  <hr style="border-color: white;"/>
  <?= ListView::widget([
    'id' => 'onetimeBased',
    'dataProvider' => $productsProvider,
    'emptyText' => '<p class="text-info"><b>' . \Yii::t('app', 'There are no products available at the moment...') . '</b></p>',
    'options' => ['class' => 'list-view row d-flex justify-content-center'],
    'summary' => false,
    'itemOptions' => [
      'tag' => false,
    ],
    'itemView' => '_product',
    'viewParams' => ['mine' => $mine],
  ]);?><!--//onetimeBased-->
  <div class="row d-flex justify-content-center">
    <?php if ($mine && $mine->subscription_id !== 'sub_vip') : ?>
      <div class="col-md-5 col-sm-6">
        <?php \app\widgets\Card::begin([
          'header' => 'header-icon',
          'type' => 'card-stats',
          'icon' => '<i class="fa-brands fa-stripe"></i>',
          'encode' => false,
          'color' => 'stripe',
          'title' => '<b>' . \Yii::t('app', 'Stripe Customer Portal!') . '</b>',
          'footer' => $subscription->getPortalLink($this, Html::a('Stripe Portal', ['/subscription/default/redirect-customer-portal'], [
            'class' => 'h4 font-weight-bold btn btn-outline-stripe btn-block',
            'id' => 'stripePortal',
          ])),
        ]); ?>
        Go to your Stripe Portal to manage your payment details or subscription package.
        <?php \app\widgets\Card::end(); ?>

      </div>
    <?php endif; ?>
  </div>
</div>
<?php
$this->registerJs('
var stripe = Stripe("' . \Yii::$app->sys->stripe_publicApiKey . '");

var createCheckoutSession = function(priceId) {
  return fetch("' . \yii\helpers\Url::to(['/subscription/default/create-checkout-session']) . '", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      priceId: priceId,
      "' . Yii::$app->request->csrfParam . '": "' . Yii::$app->request->csrfToken . '"
    })
  }).then(function(result) {
    if (!result.ok) {
      throw Error(result.status);
    }
    return result.json();
  }).catch(function(error) {
    Swal.fire({
      title: "' . \Yii::t('app', 'Oooops!') . '",
      html: "' . \Yii::t('app', 'We cannot process your request at this time. <br/>Try again later or contact the support!') . '<br/><small>[ "+error+" ]</small>",
      type: "warning",
      closeOnClickOutside: false
    });
    return false;
  });
};');
