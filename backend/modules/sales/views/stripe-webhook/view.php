<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\StripeWebhook */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stripe Webhooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerJsFile('@web/js/hljs/highlight.min.js',[
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerCssFile('@web/js/hljs/styles/a11y-dark.min.css',['depends' => [\yii\web\JqueryAsset::class]]);

?>
<div class="stripe-webhook-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'object_id',
            [
              'attribute'=>'object',
              'format'=>'markdown',
              'value'=>function($model) { return sprintf("```json\n%s\n```",$model->object);}
            ],
            'ts',
        ],
    ]) ?>

</div>

<?php
$this->registerJs(
  'hljs.highlightAll();',
  $this::POS_READY,
  'markdown-highlighter'
);
?>
