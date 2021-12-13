<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      [
        'attribute'=>'challenge_name',
        'value'=>function($model){ return sprintf("ID: %d / %s",$model->challenge_id,$model->challenge->name);},
        'headerOptions' => ['style' => 'width:20vw'],
      ],
      'timer',
      'rating',
      'first:boolean',
      'created_at:datetime',
      [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{delete}',
          'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['/activity/challenge-solver/'.$action, 'player_id' => $model->player_id,'challenge_id'=>$model->challenge_id]);
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
