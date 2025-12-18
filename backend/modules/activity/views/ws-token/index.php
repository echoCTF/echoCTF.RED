<?php

use app\modules\activity\models\WsToken;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsTokenSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Ws Tokens');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ws-token-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Ws Token'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'token',
        'contentOptions' => ['style' => 'white-space: nowrap;font-family: monospace;font-size:1.1em;'],
        'filterOptions' => ['style' => 'white-space: nowrap; font-family: monospace;',],
      ],
      [
        'attribute' => 'player_id',
        'value' => function ($model) {
          return $model->player_id ? ($model->player->username ?? null) : null;
        },
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
        'urlCreator' => function ($action, WsToken $model, $key, $index, $column) {
          return Url::toRoute([$action, 'token' => $model->token]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>