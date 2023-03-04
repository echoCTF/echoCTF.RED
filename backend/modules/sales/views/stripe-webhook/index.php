<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\StripeWebhookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stripe Webhooks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="stripe-webhook-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to truncate this table?'),
                'method' => 'post',
            ],
        ]) ?>

    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'attribute' => 'id',
               'headerOptions' => ['style' => 'width:20px'],
            ],
            'type',
            [
              'attribute'=>'object_id',
              'headerOptions' => ['style' => 'min-width: 50px; width:10%'],
              'format'=>'raw',
              'value'=>function($model) { return sprintf("<small>%s</small>",Html::encode(substr($model->object_id,0,25)));}
            ],
            [
              'attribute'=>'object',
              'format'=>'raw',
              'value'=>function($model) { return sprintf("<small><pre>%s</pre></small>",Html::encode(substr($model->object,0,80)));}
            ],
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
