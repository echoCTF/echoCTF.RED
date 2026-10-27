<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerTargetTrafficSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Target Traffic');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

$sizeCell = function ($value) {
  return Html::tag('span', Yii::$app->formatter->asShortSize($value, 2), [
    'title' => $value,
    'style' => 'border-bottom: 1px dashed #666; cursor: help;',
  ]);
};
?>
<div class="player-target-traffic-index">

  <h1><?= Html::encode($this->title) ?></h1>
  <p class="text-muted">
    <?= Yii::t('app', 'Packets and bytes exchanged between each player and each target, collected from pflow exports by <code>traffic/collect</code>. Sent is player to target, received is target to player. Connections are counted when their pf state ends.') ?>
  </p>

  <p>
    <?= Html::a(Yii::t('app', 'Delete filtered'), ['delete-filtered'], [
      'class' => 'btn btn-warning',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete the currently filtered records?'),
        'method' => 'post',
        'params' => $searchModel->attributes,
      ],
    ]) ?>
    <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete all the records?'),
        'method' => 'post',
      ],
    ]) ?>
  </p>

  <?php Pjax::begin(); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'app\components\columns\ProfileColumn'],
      ['class' => 'app\components\columns\TargetColumn'],
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
        'headerOptions' => ['style' => 'width: 10rem'],
      ],
      [
        'attribute' => 'last_seen',
        'headerOptions' => ['style' => 'width: 10rem'],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>
