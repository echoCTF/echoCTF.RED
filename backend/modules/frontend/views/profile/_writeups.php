<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
          'attribute'=>'fqdn',
          'format'=>'html',
          'contentOptions'=>['class'=>'text-nowrap'],
          'value'=>function($model){return sprintf("%s (<small>%s</small>)",$model->target->name,$model->target->ipoctet);},
        ],
        'approved:boolean',
        [
          'attribute'=>'status',
          'filter'=>['OK'=>'OK','PENDING'=>'PENDING','REJECTED'=>'REJECTED','NEEDS FIXES'=>'NEEDS FIXES'],
        ],
        'created_at',
        'updated_at',

        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{approve} {view} {update} {delete}',
          'visibleButtons' => [
              'approve' => function ($model) {
                  return !$model->approved || $model->status!=='OK';
              },
          ],
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/activity/writeup/'.$action, 'player_id' => $model->player_id,'target_id'=>$model->target_id]);
          },
          'buttons' => [
              'approve' => function ($url) {
                  return Html::a(
                      '<span class="glyphicon glyphicon-ok"></span>',
                      $url,
                      [
                          'title' => 'Approve writeup',
                          'data-method'=>'post',
                          'data-pjax' => '0',
                      ]
                  );
              },
          ],
        ],
    ],
]); ?>
