<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'vpn-historyPJ','enablePushState'=>false,'enableReplaceState'=>false,]);?>
<h5>VPN History</h5>
<?= GridView::widget([
    'id'=>'vpn-history',
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
Pjax::end();
