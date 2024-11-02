<?php

use app\modules\activity\models\PlayerDisconnectQueueHistory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueueHistorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Disconnect Queue Histories');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-disconnect-queue-history-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
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
      'id',
      ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'username'],
      'created_at',
      [
        'class' => ActionColumn::class,
        'template' => '{delete}',
        'urlCreator' => function ($action, PlayerDisconnectQueueHistory $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>