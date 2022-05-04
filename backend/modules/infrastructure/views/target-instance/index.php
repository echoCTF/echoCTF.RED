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
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap\Modal::end();

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
              'value'=>function($model){return $model->server_id.': '.$model->server->name;}
            ],
            'ipoctet',
            [
                'attribute'=>'reboot',
                'value'=>'rebootVal',
                'filter' => [0=>'Start / Do Nothing',1=>'Restart',2=>'Destroy'],
                'headerOptions' => ['style' => 'width:7em'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
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
                      '<span class="glyphicon glyphicon-refresh"></span>',
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
                      '<span class="glyphicon glyphicon-off"></span>',
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
