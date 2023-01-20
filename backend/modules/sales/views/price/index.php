<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prices'), 'url' => ['index']];
?>
<div class="price-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
      <?= Html::a(Yii::t('app', 'Create Price'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'attribute'=>'id',
              'format'=>'raw',
              'value'=>function($model){
                if($model->id=='prod_vip')
                  return "<small><abbr title='VIP Product'>prod_vip</abbr></small>";
                elseif($model->id!='')
                      return '<small>'.Html::a($model->id,"https://dashboard.stripe.com/prices/".$model->id,['target'=>'_blank']).'</small>';
                return null;
              }
            ],
            'active:boolean',
            [
              'attribute'=>'currency',
              'contentOptions'=>['style'=>'text-transform: uppercase;'],
            ],
            [
              'attribute'=>'metadata',
              'value'=>function($model) {
                if($model->metadata==='[]')
                  return null;
              }
            ],
            'nickname',
            [
              'attribute'=>'product_id',
              'format'=>'raw',
              'contentOptions'=>['style'=>'white-space: nowrap;'],
              'value'=>function($model){
                  return sprintf('<small><abbr title="%s">%s</abbr></small>',$model->product_id,$model->product->name);
              }
            ],
            'recurring_interval',
            'interval_count',
            [
              'attribute'=>'unit_amount',
              'format'=>'raw',
              'value'=>function($model){return Yii::$app->formatter->asCurrency($model->unit_amount/100,$model->currency);}
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
