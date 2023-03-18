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
        [
          'attribute'=>'timer',
          'format'=>'html',
          'value'=>function($model){return sprintf('<abbr title="%s">%s</abbr>',Yii::$app->formatter->asDuration($model->timer),$model->timer);}
        ],
        'first:boolean',
        'rating',
        [
          'attribute'=>'created_at',
          'format'=>'html',
          'value'=>function($model){return sprintf('<abbr title="%s">%s</abbr>',Yii::$app->formatter->asRelativeTime($model->created_at),$model->created_at);}
        ],
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
