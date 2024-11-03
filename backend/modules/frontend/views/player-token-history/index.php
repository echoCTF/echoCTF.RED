<?php

use app\modules\frontend\models\PlayerTokenHistory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerTokenHistorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Token Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-token-history-index">

  <h1><?= Html::encode($this->title) ?></h1>
  <p>
    <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete all records?'),
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
      ['class' => 'app\components\columns\ProfileColumn',  'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
      'type',
      'token',
      'description:ntext',
      'expires_at',
      'created_at',
      'ts',
      [
        'class' => ActionColumn::class,
        'urlCreator' => function ($action, PlayerTokenHistory $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>