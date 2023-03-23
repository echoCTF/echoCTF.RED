<?php

use yii\helpers\Html;
use app\modules\frontend\models\Player;
use app\modules\sales\models\Product;
use app\modules\sales\models\PlayerSubscription;
use app\widgets\statscard\StatsCardModel;

$this->title = Yii::t('app', 'Sales Dashboard');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-customer-index">

  <h1><?= Html::encode($this->title) ?></h1>
  <div class="body-content">
    <div class="row">
      <div class="col-xl-3 col-lg-6">
        <?= StatsCardModel::widget([
          'icon' => 'fas fa-envelope-open-text',
          'color' => 'primary',
          'modelClass' => 'app\modules\sales\models\StripeWebhook',
          'field' => 'ts',
          'title' => Html::a('Webhooks', ['/sales/stripe-webhook/index'], ['title' => 'Webhooks'])
        ]); ?>
      </div>
      <div class="col-xl-3 col-lg-6">
        <?= StatsCardModel::widget([
          'icon' => 'fab fa-cc-stripe',
          'color' => 'primary',
          'modelClass' => "app\modules\sales\models\PlayerSubscription",
          'total' => PlayerSubscription::find()->count() . ' ' . '<small class="text-muted"><abbr title="Active">' . PlayerSubscription::find()->active()->count() . '</abbr>/</small><sub class="text-muted fs-6">'.PlayerSubscription::find()->vip()->active()->count().' VIP</sub>',
          'title' => Html::a('Subscriptions', ['/sales/player-subscription/index'])
        ]); ?>
      </div>
      <div class="col">
        <div class="row">
          <h2><?= Html::a('Customers &raquo;', ['/sales/player-customer/index']) ?> <?= Player::find()->where('stripe_customer_id is not null')->count() ?></h2>
        </div>
        <div class="row">
          <h2><?= Html::a('Products &raquo;', ['/sales/product/index']) ?> <?= Product::find()->count() ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>