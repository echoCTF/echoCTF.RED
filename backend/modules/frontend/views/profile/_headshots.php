<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
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
          'template' => '{view}{delete}',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/activity/headshot/'.$action, 'player_id' => $model->player_id,'target_id'=>$model->target_id]);
          }
        ],
    ],
]);
