<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'findingsPJ','enablePushState'=>false,'enableReplaceState'=>false,]);?>
<?= GridView::widget([
    'id'=>'findings',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'name',
        'pubname',
        'points',
        [
          'attribute'=>'protocol',
          'filter'=>['tcp'=>'TCP', 'udp'=>'UDP', 'icmp'=>'ICMP'],
        ],
        'port',
        'stock',
        [
          'attribute'=>'discovered',
          'value'=>function($model) {return count($model->playerFindings);},
          'filter'=>[0=>'No', 1=>'Yes'],
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/gameplay/finding/'.$action, 'id' => $model->id]);
          }
        ],
    ],
]);
Pjax::end();
