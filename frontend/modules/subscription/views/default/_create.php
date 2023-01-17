<?php
/* @var $this yii\web\View */

use \yii\helpers\Html;
use yii\widgets\ListView;

$subscription = Yii::$app->getModule('subscription');

?>
<div class="container">
  <?php
  echo ListView::widget([
    'dataProvider' => $dataProvider,
    'emptyText' => '<p class="text-info"><b>' . \Yii::t('app', 'There are no subscriptions available at the moment...') . '</b></p>',
    'options' => ['class' => 'list-view row d-flex justify-content-center'],
    'summary' => false,
    'itemOptions' => [
      'tag' => false,
    ],
    'itemView' => '_product',
    'viewParams' => ['mine' => $mine],
  ]); ?>

  <div class="row d-flex justify-content-center">
<?php if ($mine && $mine->subscription_id !== 'sub_vip') : ?>
    <div class="col-md-5 col-sm-6">
    <?php \app\widgets\Card::begin([
        'header' => 'header-icon',
        'type' => 'card-stats',
        'icon' => '<i class="fa-brands fa-stripe"></i>',
        'encode'=>false,
        'color' => 'stripe',
        'title' => '<b>'.\Yii::t('app', 'Stripe Customer Portal!').'</b>',
        'footer' =>$subscription->getPortalLink($this,Html::a('Stripe Portal', ['/subscription/default/redirect-customer-portal'], [
          'class' => 'h4 font-weight-bold btn btn-outline-stripe btn-block',
          'id'=>'stripePortal',
      ])),
    ]); ?>
    Go to your Stripe Portal to manage your payment details or subscription package.
    <?php \app\widgets\Card::end(); ?>

    </div>
<?php endif; ?>

    <div class="col-md-5 col-sm-6">
      <?php \app\widgets\Card::begin([
        'header' => 'header-icon',
        'type' => 'card-stats',
        'icon' => '<i class="fas fa-flag"></i>',
        'color' => 'rose',
        'encode'=>false,
        'title' => '<b>'.\Yii::t('app', 'On premises CTF or Hackathon?').'</b>',
        'footer' => Html::mailto(\Yii::t('app', 'Contact us'), 'info@echothrust.com', [
          'class' => 'h4 font-weight-bold btn btn-outline-danger btn-block'
        ]),
      ]); ?>
      <?= \Yii::t('app', 'Want to run or host your own CTF, Hackathon or Cybersecurity exercises?') ?>
      <?php \app\widgets\Card::end(); ?>

    </div>
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
    Swal.fire(
      "' . \Yii::t('app', 'Oooops!') . '",
      "' . \Yii::t('app', 'We cannot process your request at this time. <br/>Try again later or contact the support!') . '<br/><small>[ "+error+" ]</small>",
      "warning"
    );
    return false;
  });
};');
