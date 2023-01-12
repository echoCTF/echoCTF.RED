<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'headshotsPJ','enablePushState'=>false,'enableReplaceState'=>false,]);?>
<?= GridView::widget([
    'id'=>'heasthots',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    //'filterUrl' => ['frontend/profile/headshots','id' => $searchModel->player_id, '#' => 'headshot-tab'],
    'columns' => [
        'target_id',
        [
          'attribute'=>'name',
          'value'=>'target.name',
        ],
        [
          'attribute'=>'ipoctet',
          'value'=>'target.ipoctet',
        ],
        'timer',
        'first:boolean',
        'rating',
        'created_at',

        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{delete}',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/activity/headshot/'.$action, 'player_id' => $model->player_id,'target_id'=>$model->target_id]);
          }
        ],
    ],
]);
Pjax::end();
