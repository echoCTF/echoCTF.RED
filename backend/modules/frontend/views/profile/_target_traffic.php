<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\grid\GridView;

$sizeCell = function ($value) {
  return Html::tag('span', Yii::$app->formatter->asShortSize($value, 2), [
    'title' => $value,
    'style' => 'border-bottom: 1px dashed #666; cursor: help;',
  ]);
};
?>
<?php Pjax::begin(['id' => 'target-trafficPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Target Traffic</h5>
<p class="text-muted small">Sent is player to target, received is target to player. Connections are counted when their pf state ends.</p>
<?= GridView::widget([
  'id' => 'target-traffic',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    ['class' => 'yii\grid\SerialColumn'],
    ['class' => 'app\components\columns\TargetColumn', 'contentOptions' => ['class' => 'text-nowrap']],
    [
      'attribute' => 'packets_sent',
      'format' => 'integer',
    ],
    [
      'attribute' => 'packets_received',
      'format' => 'integer',
    ],
    [
      'attribute' => 'bytes_sent',
      'format' => 'raw',
      'value' => function ($model) use ($sizeCell) {
        return $sizeCell($model->bytes_sent);
      },
    ],
    [
      'attribute' => 'bytes_received',
      'format' => 'raw',
      'value' => function ($model) use ($sizeCell) {
        return $sizeCell($model->bytes_received);
      },
    ],
    [
      'attribute' => 'first_seen',
      'contentOptions' => ['class' => 'text-nowrap'],
    ],
    [
      'attribute' => 'last_seen',
      'contentOptions' => ['class' => 'text-nowrap'],
    ],
  ],
]); ?>

<?php Pjax::end(); ?>
