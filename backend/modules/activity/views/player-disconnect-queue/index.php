<?php

use app\modules\activity\models\PlayerDisconnectQueue;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueueSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Disconnect Queues');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-disconnect-queue-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Player Disconnect Queue'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Truncate', ['truncate'], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => 'Are you sure you want to delete all the entries?',
        'method' => 'post',
      ],
    ]) ?>

  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],

      ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'username'],
      'created_at',
      [
        'class' => ActionColumn::class,
        'template' => '{delete}',
        'urlCreator' => function ($action, PlayerDisconnectQueue $model, $key, $index, $column) {
          return Url::toRoute([$action, 'player_id' => $model->player_id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>