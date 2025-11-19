<?php

use \yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->sys->event_name . " " . \Yii::t('app', "Subscriptions");
$this->_url = \yii\helpers\Url::to([null], 'https');
$this->registerJsFile("https://js.stripe.com/v3/", ['position' => 1]);
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.min.js");
//$this->registerJsFile("/js/plugins/sweetalert2.js");

$this->registerCss(file_get_contents(__DIR__ . "/pricing.css"));
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
      if ($mine && $mine->active && $mine->subscription_id !== 'sub_vip') {
        echo $this->render('_update', ['mine' => $mine,]);
      } else if (trim(\Yii::$app->user->identity->stripe_customer_id) !== "") {
        echo $this->render('_stripe_portal', ['mine' => $mine,]);
      }
    }
    ?>
    <!--
    <div class="row d-flex justify-content-center">
      <div class="col-md-5 col-sm-6">
        <?php \app\widgets\Card::begin([
          'header' => 'header-icon',
          'type' => 'card-stats',
          'icon' => '<i class="fas fa-flag"></i>',
          'color' => 'rose',
          'encode' => false,
          'title' => '<b>' . \Yii::t('app', 'On premises CTF or Hackathon?') . '</b>',
          'footer' => Html::mailto(\Yii::t('app', 'Contact us'), 'info@echothrust.com', [
            'class' => 'h4 font-weight-bold btn btn-outline-danger btn-block'
          ]),
        ]); ?>
        <?= \Yii::t('app', 'Want to run or host your own CTF, Hackathon or Cybersecurity exercises?') ?>
        <?php \app\widgets\Card::end(); ?>
      </div>
    </div>
    -->

  </div>
</div>