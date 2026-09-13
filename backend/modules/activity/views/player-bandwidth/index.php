<?php

use app\modules\activity\models\PlayerBandwidth;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerBandwidthSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Bandwidth');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-bandwidth-index">

  <h1><?= Html::encode(Yii::t('app', 'Player Bandwidth Records')) ?></h1>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width: 6rem'],
      ],
      ['class' => 'app\components\columns\ProfileColumn'],
      [
        'attribute' => 'vpn_local_address',
        'headerOptions' => ['style' => 'width: 10rem'],
        'value' => function ($model) {
          return long2ip($model->vpn_local_address);
        },
      ],
      [
        'attribute' => 'bytes_received',
        'format' => 'raw',
        'value' => function ($model) {
          return \yii\helpers\Html::tag('span', Yii::$app->formatter->asShortSize($model->bytes_received, 2), [
            'title' => $model->bytes_received,
            'style' => 'border-bottom: 1px dashed #666; cursor: help;',

          ]);
        },
      ],
      [
        'attribute' => 'bytes_sent',
        'format' => 'raw',
        'value' => function ($model) {
          return \yii\helpers\Html::tag('span', Yii::$app->formatter->asShortSize($model->bytes_sent, 2), [
            'title' => $model->bytes_sent,
            'style' => 'border-bottom: 1px dashed #666; cursor: help;',
          ]);
        },
      ],
      [
        'attribute' => 'duration',
        'format' => 'raw',
        'headerOptions' => ['style' => 'width: 16rem'],
        'value' => function ($model) {
          return \yii\helpers\Html::tag('span', Yii::$app->formatter->asDuration($model->duration, ' '), [
            'title' => $model->duration,
            'style' => 'border-bottom: 1px dashed #666; cursor: help;',
          ]);
        },
      ],
      [
        'attribute' => 'ts',
        'headerOptions' => ['style' => 'width: 10rem'],
      ],
      [
        'class' => ActionColumn::className(),
        'urlCreator' => function ($action, PlayerBandwidth $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>