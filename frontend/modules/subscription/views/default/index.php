<?php

use \yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->sys->event_name . " " . \Yii::t('app', "Subscriptions");
$this->_url = \yii\helpers\Url::to([null], 'https');
$this->registerJsFile("https://js.stripe.com/v3/", ['position' => 1]);
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.min.js");
//$this->registerJsFile("/js/plugins/sweetalert2.js");

$this->registerCss(file_get_contents(__DIR__ . "/pricing.css"));
$this->_fluid='-fluid';
?>
<div class="subscription-index">
  <div class="body-content">

    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
      <h1 class="display-4 text-primary"><?= \Yii::t('app', 'Level up your game') ?></h1>
      <p class="lead"><?= \Yii::t('app', 'Subscriptions help us to keep the platform running and allows us to focus on developing new content.') ?></br>
        <?= \Yii::t('app', 'Pick your subscription type and start hacking.') ?></p>
    </div>
    <?php
    if (Yii::$app->sys->subscriptions_emergency_suspend === true) {
      echo $this->render('_suspended');
    } else {
      echo $this->render('_create', ['mine' => $mine, 'subscriptionsProvider' => $subscriptionsProvider, 'productsProvider' => $productsProvider]);
      echo '<div class="d-flex justify-content-center row">';
      if (($mine && $mine->active && $mine->subscription_id !== 'sub_vip')|| intval($myproducts->count())>0) {
        echo $this->render('_update', ['mine' => $mine,'myproductsProvider'=>$myproductsProvider,'myproducts'=>$myproducts]);
      }
      if(\Yii::$app->user->identity->playerPaymentHistory) {
        echo $this->render('_payments_card');
      }
      if (trim(\Yii::$app->user->identity->stripe_customer_id) !== "") {
        echo $this->render('_stripe_portal', ['mine' => $mine,]);
      }
      echo '</div>';
    }
    ?>

  </div>
</div>