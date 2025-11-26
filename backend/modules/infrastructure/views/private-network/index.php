<?php

use app\modules\infrastructure\models\PrivateNetwork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetworkSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Private Networks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-network-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Private Network'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'id',
      ['class' => 'app\components\columns\ProfileColumn'],
      'name',
      'team_accessible:boolean',
      'created_at',
      [
        'template' => '{view} {update} {delete} {notify}',
        'class' => '\app\components\columns\ActionColumn',
        'notifyIdValue' => function ($model) { return $model->player_id; },
        'urlCreator' => function ($action, $model, $key, $index, $column) {
          if ($action === 'notify') {
            return \yii\helpers\Url::to(['/frontend/player/notify', 'id' => $model->player_id]);
          }
          return \yii\helpers\Url::to(['/infrastructure/private-network/'.$action, 'id' => $model->id]);
        },
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>