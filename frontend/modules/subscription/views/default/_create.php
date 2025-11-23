<?php
/* @var $this yii\web\View */

use \yii\helpers\Html;
use yii\widgets\ListView;

$subscription = Yii::$app->getModule('subscription');

?>
<div class="container">
  <?php if ($mine===null || !$mine->active): ?>
    <?= ListView::widget([
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
    ]); ?><!--//subscriptionBased-->
    <hr style="border-color: white;" />
  <?php endif; ?>
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
  ]); ?><!--//onetimeBased-->
</div>
<hr style="border-color: white;"/>
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
