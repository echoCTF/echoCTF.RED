<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'treasuresPJ','enablePushState'=>false,'enableReplaceState'=>false,]);?>
<?= GridView::widget([
    'id'=>'treasures',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'name',
        'pubname',
        [
          'attribute'=>'category',
          'filter'=>['other'=>'other', 'env'=>'env', 'root'=>'root', 'system'=>'system', 'app'=>'app'],
        ],
        'points',
        [
          'attribute'=>'player_type',
          'filter'=>['offense'=>'Offense', 'defense'=>'Defense', 'both'=>'Both'],
        ],
        'appears',
        [
          'attribute'=>'code',
          'format'=>'raw',
          'value'=>function($model) {return '<abbr title="'.Html::encode($model->code).'">'.Html::encode(substr($model->code, 0, 15)).'</abbr>';},
        ],
        [
          'attribute'=>'discovered',
          'value'=>function($model) {return count($model->playerTreasures);},
          'filter'=>[0=>'No', 1=>'Yes'],
        ],
        'weight',
        [
          'class' => 'yii\grid\ActionColumn',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/gameplay/treasure/'.$action, 'id' => $model->id]);
          }
        ],
    ],
]);
Pjax::end();
