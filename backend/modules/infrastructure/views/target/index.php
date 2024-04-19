<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Targets', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="target-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Spin All', ['spin', 'id' => 'all'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Pull All', ['pull', 'id' => 'all'], ['class' => 'btn btn-warning']) ?>
    <?= Html::a('Statistics', ['statistics'], ['class' => 'btn btn-info']) ?>
    <?= Html::a('Container Status', ['status'], ['class' => 'btn btn-info']) ?>
    <?= Html::a('Docker compose', ['docker-compose'], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'id',
      [
        'attribute'=>'name',
        'contentOptions'=>['style'=>'white-space: nowrap;',],
      ],
      [
        'label' => 'IP',
        'attribute' => 'ipoctet',
        'contentOptions'=>['class'=>'font-weight-light small text-monospace'],
      ],
      [
        'label' => 'Server',
        'attribute' => 'server',
        'contentOptions'=>['class'=>'font-weight-light small text-monospace'],
      ],
      [
        'label' => 'Network',
        'attribute' => 'network_name',
        'value' => 'network.name'
      ],
      [
        'attribute' => 'status',
        'format'=>'raw',
        'contentOptions'=>['class'=>'text-center'],
        'value'=>function($model){
          return sprintf('<abbr title="%s"><i class="bi bi-target-status-%s"></i></abbr>',$model->status,$model->status);
        },
        'filter' => $searchModel->statuses,
      ],
      'scheduled_at:dateTime:Scheduled',
      'rootable:boolean',
      'instance_allowed:boolean:Instances',
      'require_findings:boolean:Req. Findings',
      'active:boolean',
      'timer:boolean',
      [
        'attribute' => 'difficulty',
        'contentOptions'=>['class'=>'font-weight-light small text-monospace'],
        'filter' => [
          0 => "beginner",
          1 => "basic",
          2 => "intermediate",
          3 => "advanced",
          4 => "expert",
          5 => "guru",
          6 => "insane",
        ],
        'value' => 'difficultyString'
      ],

      [
        'label' => 'Headshots',
        'attribute' => 'headshot',
        'value' => function ($model) {
          return count($model->headshots);
        }
      ],
      [
        'label' => 'Points',
        'attribute' => 'pts',
        'value' => function ($model) {
          return $model->findingPoints + $model->treasurePoints;
        }
      ],
      'weight',
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{full-view} {spin} {pull} {view} {update} {delete}',
        'buttons' => [
          'full-view' => function ($url) {
            return Html::a(
              '<i class="fas fa-server"></i>',
              $url,
              [
                'title' => 'Target Full View',
                'data-pjax' => '0',
                'data-toggle' => 'tooltip',
              ]
            );
          },

          'spin' => function ($url) {
            return Html::a(
              '<i class="bi bi-power"></i>',
              $url,
              [
                'title' => 'Spin container',
                'data-pjax' => '0',
                'data-method' => 'POST',
              ]
            );
          },
          'pull' => function ($url) {
            return Html::a(
              '<i class="bi bi-cloud-download"></i>',
              $url,
              [
                'title' => 'Pull container image',
                'data-pjax' => '0',
                'data-method' => 'POST',
              ]
            );
          },
        ],
        'header' => Html::a(
          '<i class="bi bi-check-circle-fill"></i>',
          ['activate-filtered'],
          [
            'title' => 'Mass activate all filtered targets?',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => @\Yii::$app->request->queryParams['TargetSearch'],
              'confirm' => 'Are you sure you want to activate all currently filtered targets?',
            ],
          ]
        ) . ' ' . Html::a(
          '<i class="bi bi-power"></i>',
          ['spin-filtered'],
          [
            'title' => 'Mass spin all filtered containers?',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => @\Yii::$app->request->queryParams['TargetSearch'],
              'confirm' => 'Are you sure you want to spin all currently filtered targets?',
            ],
          ]
        ) . ' ' . Html::a(
          '<i class="bi bi-cloud-download-fill"></i>',
          ['pull-filtered'],
          [
            'title' => 'Mass pull target images',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => @\Yii::$app->request->queryParams['TargetSearch'],
              'confirm' => 'Are you sure you want to pull all the currently filtered target images?',
            ],
          ]
        ) . ' ' . Html::a(
          '<i class="bi bi-trash"></i>',
          ['delete-filtered'],
          [
            'title' => 'Mass Delete targets',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => @\Yii::$app->request->queryParams['TargetSearch'],
              'confirm' => 'Are you sure you want to delete the currently filtered targets?',
            ],
          ]
        ),

      ],
    ],
  ]); ?>

</div>