<?php
/* @var $this yii\web\View */
use \yii\helpers\Html;
use \yii\helpers\Url;
$subscription=Yii::$app->getModule('subscription');
?>
<div class="d-flex justify-content-center">
  <div class="alert col-md-6" role="alert">
    <h4>You currently have an active <em class="active"><?=$mine->product->name?></em> subscription</h4>
<?php if($mine->product->shortcode==='basicSubscription'):?>
  <p>
    If you wish to gain access to all the networks you will have to update your
    current subscription. Click on the button below to go to your Stripe
    subscription portal and update your subscription.
  </p>
<?php else:?>
  <p>
    If you wish to modify or cancel your subscription, click on the button
    below to go to your Stripe subscription portal.
  </p>
<?php endif;?>
    <p>
    <?=$subscription->getPortalButton($this)?>
    </p>
  </div>
</div>
