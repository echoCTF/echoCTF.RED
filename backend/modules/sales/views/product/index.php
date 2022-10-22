<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products');
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
            'id',
            'name',
            'shortcode',
            //'description:ntext',
            'active:boolean',
            //'livemode:boolean',
            //'htmlOptions:ntext',
            //[
            //    'attribute'=>'metadata',
            //],
            'price_id',
            [
              'attribute'=>'unit_amount',
              'format'=>'raw',
              'value'=>function($model){return Yii::$app->formatter->asCurrency($model->unit_amount/100,$model->currency);}
            ],
            'weight:integer',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
