<?php
/* @var $this yii\web\View */

use app\modules\sales\models\PlayerSubscription;
use \yii\helpers\Html;

$this->title = Yii::$app->sys->event_name . " Subscriptions";
$this->_url = \yii\helpers\Url::to([null], 'https');
//$this->registerJsFile("https://js.stripe.com/v3/",['position'=>1]);
//$this->registerCss(file_get_contents(__DIR__."/pricing.css"));
?>
<div class="site-index">
  <div class="body-content">
    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
      <h1 class="display-4 text-primary"><?= \Yii::t('app', 'Purchase complete') ?></h1>
      <?php if (isset($mine->product->metadataObj->standalone_perk) && intval($mine->product->metadataObj->standalone_perk) > 0): ?>
        <p class="lead"><?= \Yii::t('app', 'Your {product_name} purchase is now complete. You will receive a notification informing you about its activation shortly.', ['product_name' => $mine->product->name]) ?></p>
      <?php else: ?>
        <p class="lead"><?= \Yii::t('app', 'Your {product_name} subscription purchase is complete. You will receive a notification informing you about its activation shortly.', ['product_name' => $mine->product->name]) ?></p>
      <?php endif; ?>
    </div>
    <center><?= Html::a('<b><i class="fas fa-backward"></i> ' . \Yii::t('app', 'Go back') . '</b>', ['/subscriptions'], ['class' => 'btn btn-lg btn-primary text-dark']) ?></center>
  </div>
</div>