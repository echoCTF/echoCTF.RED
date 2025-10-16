<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetOndemandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Ondemand');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Ondemand'), 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="target-ondemand-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Target Ondemand'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'target_id',
      [
        'label' => 'IP',
        'attribute' => 'ipoctet',
        'value' => 'target.ipoctet',
      ],
      [
        'label' => 'Target',
        'attribute' => 'name',
        'value' => 'target.name',
      ],
      'player_id',
      [
        'label' => 'Username',
        'attribute' => 'username',
        'value' => 'player.username',
      ],
      'state',
      'heartbeat',
      'created_at',
      //'updated_at',

      [
        'class' => '\app\components\columns\ActionColumn',
        'notifyIdValue' => function ($model) {
          return $model->player_id;
        },
        'template' => '{update} {delete} {notify} {clear}',
        'urlCreator' => function ($action, $model, $key, $index, $column) {
          if ($action === 'notify') {
            return \yii\helpers\Url::to(['/frontend/player/notify', 'id' => $model->player_id]);
          }
          return \yii\helpers\Url::to([$action, 'id' => $model->target_id]);
        },
        'header' => /*Html::a(
          '<i class="fas fa-sync"></i>',
          ['sync-all'],
          [
            'title' => 'Mass sync all states',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
            ],

          ]
        )." ".*/ Html::a(
          '<i class="fas fa-eraser"></i>',
          ['clear-all'],
          [
            'title' => 'Mass clear all states',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
            ],

          ]
        ),
        'buttons' => [
          'sync' => function ($url, $model) {
            return Html::a('<i class="fas fa-sync"></i>', $url, [
              'class' => '',
              'title' => Yii::t('app', 'target-ondemand-sync'),
              'data' => [
                'method' => 'post',
              ],
            ]);
          },
          'clear' => function ($url, $model) {
            return Html::a('<i class="fas fa-eraser"></i>', $url, [
              'class' => '',
              'title' => Yii::t('app', 'target-ondemand-clear'),
              'data' => [
                'method' => 'post',
              ],
            ]);
          },
        ],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>