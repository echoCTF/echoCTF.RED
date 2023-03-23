<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PlayerSubscriptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Subscriptions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-subscription-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Player Subscription'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Fetch from Stripe'), ['fetch-stripe'], ['class' => 'btn btn-warning']) ?>
    <?= Html::a(Yii::t('app', 'Delete Inactive'), ['delete-inactive'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete the inactive subscriptions? The subscription networks will also be removed.'),
                'method' => 'post',
            ],
        ]) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [

      'player_id',
      ['class' => 'app\components\columns\ProfileColumn'],
      [
        'attribute'=>'subscription_id',
        'format'=>'raw',
        'value'=>function($model){
          if($model->subscription_id=='sub_vip')
            return "<small><abbr title='VIP Subscription'>sub_vip</abbr></small>";
          elseif($model->subscription_id!='')
                return '<small>'.Html::a($model->subscription_id,"https://dashboard.stripe.com/subscriptions/".$model->subscription_id,['target'=>'_blank']).'</small>';
          return "";
        }
      ],
      //          [
      //            'attribute'=>'session_id',
      //            'format'=>'raw',
      //            'value'=>function($model){ return '<small>'.substr($model->session_id,0,25).'</small>';}
      //          ],
      [
        'attribute' => 'product_name',
        //'value'=>'product.name',
        'value'=>function($model){
          if($model->product)
            return $model->product->name;
          if($model->price_id==='price_vip')
            return 'VIP';
          return '';
        }
      ],
      [
        'attribute' => 'price_id',
        'format' => 'raw',
        'value' => function ($model) {
          if($model->price_id==='price_vip')
            return 'VIP';

          if ($model->price)
          {
            return sprintf('<small><abbr title="%s">%d%s every %d %s</abbr></small>', $model->price_id, intval($model->price->unit_amount / 100), strtoupper($model->price->currency), $model->price->interval_count, $model->price->recurring_interval);
          }
          return $model->price_id;
        }
      ],
      'active:boolean',
      'starting',
      'ending',
      //            'created_at',
      //            'updated_at',

      ['class' => 'yii\grid\ActionColumn'],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>