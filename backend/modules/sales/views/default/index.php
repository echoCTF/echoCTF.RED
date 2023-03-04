<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PlayerCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use app\modules\frontend\models\Player;
use app\modules\sales\models\Product;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\StripeWebhook as Webhook;
$this->title = Yii::t('app', 'Sales Dashboard');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
      <div class="col-lg-3">
          <h2><?= Html::a('Customers &raquo;', ['/sales/player-customer/index']) ?> <?=Player::find()->where('stripe_customer_id is not null')->count()?></h2>
      </div>
      <div class="col-lg-3">
          <h2><?= Html::a('Subscriptions &raquo;', ['/sales/player-subscription/index']) ?> <?=PlayerSubscription::find()->count()?></h2>
          <ul>
            <li class="h4">Active: <?=PlayerSubscription::find()->active()->count()?></li>
          </ul>
      </div>
      <div class="col-lg-3">
          <h2><?= Html::a('Products &raquo;', ['/sales/product/index']) ?> <?=Product::find()->count()?></h2>
      </div>
      <div class="col-lg-3">
          <h2><?= Html::a('Webhooks &raquo;', ['/sales/stripe-webhook/index']) ?> <?=Webhook::find()->count()?></h2>
      </div>
    </div>
</div>
