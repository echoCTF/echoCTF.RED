<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instances');
$this->params['breadcrumbs'][] = $this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
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
            ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute'=>'player_id',
              'value'=>function($model){return $model->player_id.': '.$model->player->username;}
            ],
            [
              'attribute'=>'target_id',
              'value'=>function($model){return $model->target_id.': '.$model->target->name;}
            ],
            'server_id',
            [
              'attribute'=>'ipoctet',
              'value'=>'ipoctet',
            ],
            [
              'attribute'=>'reboot',
              'value'=>'rebootVal',
            ],
            'created_at',
            'updated_at',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{restart} {destroy} {view} {update} {delete}',
              'buttons' => [
                'restart' => function($url) {
                  return Html::a(
                      '<span class="glyphicon glyphicon-refresh"></span>',
                      $url,
                      [
                        'title' => 'Restart instance',
                        'data-pjax' => '0',
                        'data-method' => 'POST',
                      ]
                  );
                },
                'destroy' => function($url) {
                  return Html::a(
                      '<span class="glyphicon glyphicon-off"></span>',
                      $url,
                      [
                        'title' => 'Spin container',
                        'data-pjax' => '0',
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
