<?php
/* @var $this yii\web\View */

use \yii\helpers\Html;
use \yii\helpers\Url;
use yii\widgets\ListView;

$subscription = Yii::$app->getModule('subscription');
?>
<?= ListView::widget([
  'id' => false,
  'dataProvider' => $myproductsProvider,
  'emptyText' => false,
  'options' => ['tag' => false],
  'summary' => false,
  'itemOptions' => ['tag' => 'div', 'class' => 'col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch'],
  'itemView' => '_my_product',
]); ?><!--//onetimeBased-->
<?php if (Yii::$app->user->identity->subscription): ?>
  <div class="col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
    <?php \app\widgets\Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats reverse',
      'icon' => Html::img('/images/'.$subscription->product->shortcode.'.png', ['style' => 'width: 50px; height: 50px']),
      'encode' => false,
      'color' => 'danger',
      'title' => '<b>' . $subscription->product->name . '</b>',
      'subtitle'=>'<small>Expires in '.$subscription->expires.'</small>',
      'footer' => Html::a(\Yii::t('app', 'Cancel subscription'), ['/subscription/default/cancel-subscription'], [
        'class' => 'h4 font-weight-bold btn btn-outline-danger btn-block',
        'data' => [
          'method' => 'post',
        ],
      ]),
    ]); ?>
    <p>It includes <?= implode(", ", explode("\n", strip_tags(\Yii::$app->user->identity->subscription->product->perks))) ?></p>
    <?php \app\widgets\Card::end(); ?>


  </div>
<?php endif; ?>