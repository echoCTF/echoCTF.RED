<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [

        'id',
        [
          'attribute'=>'vpn_remote_address',
          'value'=>function($model) {return long2ip($model->vpn_remote_address);},
        ],
        [
          'attribute'=>'vpn_local_address',
          'value'=>function($model) {return long2ip($model->vpn_local_address);},
        ],
        'ts',

        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{delete}',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/activity/player-vpn-history/'.$action, 'id' => $model->id]);
          }
        ],
    ],
]);
