<?php

use \yii\helpers\Html;

$subscription = \Yii::$app->getModule('subscription');
?>
  <div class="col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
    <?php \app\widgets\Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => '<i class="fas fa-receipt"></i>',
      'encode' => false,
      'color' => 'primary',
      'title' => '<b>' . \Yii::t('app', 'Payment History') . '</b>',
      'footer' => Html::a('Payment History', ['/subscription/payments'], [
        'class' => 'h4 font-weight-bold btn btn-outline-primary btn-block',
      ]),
    ]); ?>
    See your payment history including subscriptions and extras.
    <?php \app\widgets\Card::end(); ?>
  </div>