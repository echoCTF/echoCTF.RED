<?php
/* @var $this yii\web\View */
use \yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="container">
  <?php echo ListView::widget([
      'dataProvider' => $dataProvider,
      'emptyText'=>'<p class="text-info"><b>There are no subscriptions available at the moment...</b></p>',
      'options'=>['class'=>'list-view row d-flex justify-content-center'],
      'summary'=>false,
      'itemOptions' => [
        'tag' => false,
      ],
      'itemView' => '_product',
      'viewParams' => ['mine' => $mine],
  ]);?>

  <div class="row d-flex justify-content-center">
    <div class="col-md-5 col-sm-6">
      <?php \app\widgets\Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>'<i class="fas fa-flag"></i>',
            'color'=>'rose',
            'title'=>'On premises CTF or Hackathon?',
            //'subtitle'=>'On Premise CTF',
            'footer'=>Html::mailto('Contact us','info@echothrust.com',[
              'class'=>'h4 font-weight-bold btn btn-outline-danger btn-block'
            ]),
        ]);?>
      Want to run or host your own CTF, Hackathon or Cybersecurity excersises?
      <?php \app\widgets\Card::end();?>

    </div>
  </div>
</div>
<?php
$this->registerJs('
var stripe = Stripe("'.\Yii::$app->sys->stripe_publicApiKey.'");

var createCheckoutSession = function(priceId) {
  return fetch("'.\yii\helpers\Url::to(['/subscription/default/create-checkout-session']).'", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      priceId: priceId,
      "'.Yii::$app->request->csrfParam.'": "'.Yii::$app->request->csrfToken.'"
    })
  }).then(function(result) {
    if (!result.ok) {
      throw Error(result.status);
    }
    return result.json();
  }).catch(function(error) {
    Swal.fire(
      "Oooops!",
      "We cannot process your request at this time. <br/>Try again later or contact the support!<br/><small>[ "+error+" ]</small>",
      "warning"
    );
    return false;
  });
};');
