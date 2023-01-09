<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap\Modal::end();
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

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'ipoctet',
            'server',
            [
              'label'=>'Network',
              'attribute'=>'network_name',
              'value'=>'network.name'
            ],
            [
              'attribute'=>'status',
              'filter'=>$searchModel->statuses,
            ],
            'scheduled_at:dateTime',
            'rootable:boolean',
            'instance_allowed:boolean',
            'active:boolean',
            'timer:boolean',
            [
              'attribute'=>'difficulty',
              'filter'=>[
                0=>"beginner",
                1=>"basic",
                2=>"intermediate",
                3=>"advanced",
                4=>"expert",
                5=>"guru",
                6=>"insane",
              ],
            ],

            [
              'label' => 'Headshots',
              'attribute' => 'headshot',
              'value' => function ($model) { return count($model->headshots);}
            ],
            [
              'label' => 'Points',
              'attribute' => 'pts',
              'value' => function ($model) { return $model->findingPoints+$model->treasurePoints;}
            ],
            'weight',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{spin} {view} {update} {delete}',
              'buttons' => [
                  'spin' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon glyphicon-off"></span>',
                          $url,
                          [
                              'title' => 'Spin container',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                          ]
                      );
                  },
              ],
              'header' => Html::a(
                  '<span class="glyphicon glyphicon-ok"></span>',
                  ['activate-filtered'],
                  [
                      'title' => 'Mass activate all filtered targets?',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> @\Yii::$app->request->queryParams['TargetSearch'],
                        'confirm'=>'Are you sure you want to activate all currently filtered targets?',
                      ],
                  ]
                  ).' '.Html::a(
                  '<span class="glyphicon glyphicon-off"></span>',
                  ['spin-filtered'],
                  [
                      'title' => 'Mass spin all filtered containers?',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> @\Yii::$app->request->queryParams['TargetSearch'],
                        'confirm'=>'Are you sure you want to spin all currently filtered targets?',
                      ],
                  ]
              ).' '.Html::a(
                  '<span class="glyphicon glyphicon-cloud-download"></span>',
                  ['pull-filtered'],
                  [
                      'title' => 'Mass pull target images',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> @\Yii::$app->request->queryParams['TargetSearch'],
                        'confirm'=>'Are you sure you want to pull all the currently filtered target images?',
                      ],
                  ]
              ).' '.Html::a(
                  '<span class="glyphicon glyphicon-trash"></span>',
                  ['delete-filtered'],
                  [
                      'title' => 'Mass Delete targets',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> @\Yii::$app->request->queryParams['TargetSearch'],
                        'confirm'=>'Are you sure you want to delete the currently filtered targets?',
                      ],
                  ]
              ),

            ],
        ],
    ]);?>


</div>
