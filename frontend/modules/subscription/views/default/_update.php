<?php
/* @var $this yii\web\View */
use \yii\helpers\Html;
use \yii\helpers\Url;
$subscription=Yii::$app->getModule('subscription');
?>
<div class="d-flex justify-content-center row">
  <div class="alert col-md-6" role="alert">
    <h4><?=\Yii::t('app','You currently have an active <em class="active">{subscription_name}</em> subscription</h4>',['subscription_name'=>$mine->product->name])?>
    <p><?=\Yii::t('app','You can modify your current subscription or update your billing details by clicking the <b class="text-info" style="font-weight: 800">Manage Billing</b> button.')?></p>
    <p><?=\Yii::t('app','Alternatively, if you wish to cancel your subscription at the end of the current billing period, click the <b class="text-danger" style="font-weight: 800">Cancel Subscription</b> button.')?></p>
    <div class="row">
      <div class="col-md">
        <?=$subscription->getPortalButton($this)?>
      </div>
      <div class="col-md">
        <?=Html::a(\Yii::t('app','Cancel subscription'),['/subscription/default/cancel-subscription'],[
            'class'=>'btn btn-block btn-danger font-weight-bold',
            'data' => [
              'method' => 'post',
            ],
          ]);?>
      </div>

    </div>
  </div>
</div>
