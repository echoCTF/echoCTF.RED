<?php

use app\modules\activity\models\WsTokenHistory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsTokenHistorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Ws Token Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ws-token-history-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Ws Token History'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'id',
      ['class' => 'app\components\columns\ProfileColumn'],
      [
        'attribute' => 'token',
        'contentOptions' => ['style' => 'white-space: nowrap;font-family: monospace;font-size:1.1em;'],
        'filterOptions' => ['style' => 'white-space: nowrap; font-family: monospace;',],
      ],
      'subject_id',
      [
        'attribute' => 'is_server',
        'value' => function ($model) {
          return $model->is_server ? 'Server' : 'Player';
        },
        'filter' => [0 => 'Player', 1 => 'Server'], // optional filter dropdown
      ],
      'expires_at',
      [
        'class' => ActionColumn::className(),
        'urlCreator' => function ($action, WsTokenHistory $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>