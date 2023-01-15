<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
      <?= Html::a(Yii::t('app', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
      <?= Html::a(Yii::t('app', 'Fetch from Stripe'), ['fetch-stripe'], ['class' => 'btn btn-warning']) ?>
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
                      return '<small>'.Html::a($model->id,"https://dashboard.stripe.com/products/".$model->id,['target'=>'_blank']).'</small>';
                return "";
              }
            ],
            'name',
            'shortcode',
            'active:boolean',
            [
              'label'=>'prices',
              'value'=>function($model){ return count($model->prices);},
            ],
            'weight:integer',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
