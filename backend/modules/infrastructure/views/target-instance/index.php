<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\infrastructure\models\Server;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instances');
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();

?>
<div class="target-instance-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Target Instance'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'attribute'=>'player_id',
              'value'=>function($model){return $model->player_id.': '.$model->player->username;}
            ],
            [
              'attribute'=>'target_id',
              'value'=>function($model){return $model->target_id.': '.$model->target->name;}
            ],
            [
              'attribute'=>'server_id',
              'filter'=>ArrayHelper::map(Server::find()->orderBy(['name'=>SORT_ASC,'ip'=>SORT_ASC])->asArray()->all(),'id','name'),
              'value'=>function($model){if ($model->server) return $model->server_id.': '.$model->server->name; return null;}
            ],
            'ipoctet',
            [
                'attribute'=>'reboot',
                'value'=>'rebootVal',
                'filter' => [0=>'Start / Do Nothing',1=>'Restart',2=>'Destroy'],
                'headerOptions' => ['style' => 'width:7em'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            'team_allowed:boolean',
            [
              'attribute'=>'created_at',
              'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            [
              'attribute'=>'updated_at',
              'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{restart} {destroy} {view} {update} {delete}',
              'contentOptions' => ['style' => 'white-space: nowrap;'],
              'buttons' => [
                'restart' => function($url) {
                  return Html::a(
                      '<i class="bi bi-arrow-clockwise"></i>',
                      $url,
                      [
                        'title' => 'Restart instance',
                        'data-pjax' => '0',
                        'data-confirm'=>'You are about to start/restart this instance. Are you sure?',
                        'data-method' => 'POST',
                      ]
                  );
                },
                'destroy' => function($url) {
                  return Html::a(
                      '<i class="bi bi-power"></i>',
                      $url,
                      [
                        'title' => 'Destroy container',
                        'data-pjax' => '0',
                        'data-confirm'=>'You are about to destroy this instance. Are you sure?',
                        'data-method' => 'POST',
                      ]
                  );
                },
              ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
