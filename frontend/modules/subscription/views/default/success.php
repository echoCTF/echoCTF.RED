<?php
/* @var $this yii\web\View */
use \yii\helpers\Html;
$this->title=Yii::$app->sys->event_name ." Subscriptions";
$this->_url=\yii\helpers\Url::to([null],'https');
//$this->registerJsFile("https://js.stripe.com/v3/",['position'=>1]);
//$this->registerCss(file_get_contents(__DIR__."/pricing.css"));
?>
<div class="site-index">
    <div class="body-content">
      <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4 text-primary"><?=\Yii::t('app','Subscription completed')?></h1>
        <p class="lead"><?=\Yii::t('app','Your {event_name} subscription purchase is now complete. You will receive a notification informing you about its activation shortly.',['event_name'=>\Yii::$app->sys->event_name])?></p>
      </div>
      <?php echo $this->render('_update', [ 'mine' => $mine, ]); ?>
    </div>
</div>
