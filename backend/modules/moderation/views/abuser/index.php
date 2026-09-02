<?php

use app\modules\moderation\models\Abuser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\AbuserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Abusers');
$this->params['breadcrumbs'][] = ['label' => 'Abuser', 'url' => ['index']];
?>
<div class="abuser-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Abuser'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Truncate Abuser'), ['truncate'], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to truncate the table?'),
        'method' => 'post',
      ],
    ]) ?>
  </p>

  <?php Pjax::begin(); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:4em'],
      ],
      ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
      [
        'attribute' => 'title',
        'headerOptions' => ['style' => 'width:18em'],
        'format' => 'html',
        'value' => function ($model) {
          if (trim($model->body) != '') return $model->body;
          return $model->title;
        }
      ],
      'reason',
      'model',
      'model_id',
      'points',
      [
        'attribute' => 'resolved',
        'format' => 'boolean',
        'filter' => [0 => 'No', 1 => 'Yes']
      ],
      'created_at',
      'updated_at',
      [
        'class' => ActionColumn::class,
        'template' => ' {view} {update} {delete} {analyze} {process}',
        'header' => Html::a(
          '<i class="bi bi-trash-fill"></i>',
          ['delete-filtered'],
          [
            'title' => 'Mass delete filtered records',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
              'confirm' => 'Are you sure you want to delete the currently filtered records?',
            ],
          ]
        ),
        'visibleButtons' => [
          'process' => function ($model, $key, $index) {
            return !$model->resolved;
          },
          'analyze' => function ($model, $key, $index) {
            return !$model->resolved;
          },

        ],
        'buttons' => [
          'analyze' => function ($url, $model, $key) {
            return Html::a(
              '<i class="fas fa-diagnoses"></i>',
              Url::to(['analyze', 'id' => $model->id]),
              [
                'title' => 'Perform analysis on record',
                'data-pjax' => '0',
              ]
            );
          },
          'process' => function ($url, $model, $key) {
            return Html::a(
              '<i class="fas fa-cogs"></i>',
              Url::to(['process', 'id' => $model->id]),
              [
                'title' => 'Process record to public stream',
                'data-pjax' => '0',
              ]
            );
          },
        ],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>