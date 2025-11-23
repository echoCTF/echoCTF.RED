<?php

use \yii\helpers\Html;

$subscription = \Yii::$app->getModule('subscription');
?>
  <div class="col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
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
    Go to your Stripe Portal to manage your payment and subscription details as well as ability to update your current subscription.
    <?php \app\widgets\Card::end(); ?>
  </div>