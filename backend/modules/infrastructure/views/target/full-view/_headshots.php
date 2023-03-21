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
        'player_id',
        ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username','label'=>'Username','idkey'=>'player.profile.id','field'=>'player.username'],
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
