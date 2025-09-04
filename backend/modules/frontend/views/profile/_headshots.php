<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'headshotsPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<?= GridView::widget([
  'id' => 'heasthots',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    'target_id',
    [
      'attribute' => 'name',
      'value' => 'target.name',
    ],
    [
      'attribute' => 'timer',
      'format' => 'html',
      'value' => function ($model) {
        return Html::tag('abbr', Yii::$app->formatter->asDuration($model->timer), ['title'=> $model->timer]);
      }
    ],
    'first:boolean',
    'rating',
    [
      'attribute' => 'created_at',
      'format' => 'html',
      'value' => function ($model) {
        return Html::tag('abbr', $model->created_at_ago, ['title'=> $model->created_at]);
      }
    ],
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{delete} {zero}',
      'header' => Html::a(
        '<i class="fab fa-creative-commons-zero"></i>',
        ['/activity/headshot/zero-filtered'],
        [
          'title' => 'Zero out filtered headshots',
          'data-pjax' => '0',
          'data-method' => 'POST',
          'data' => [
            'method' => 'post',
            'params' => $searchModel->attributes,
            'confirm' => 'Are you sure you want to zero out the filtered headshots?',
          ],
        ]
      ),
      'buttons' => [
        'zero' => function ($url, $model) {
          return Html::a('<i class="fab fa-creative-commons-zero"></i>', ['/activity/headshot/zero', 'player_id' => $model->player_id, 'target_id' => $model->target_id], [
            'class' => '',
            'title' => 'Zero out headshot and points',
            'data' => [
              'confirm' => 'This operation zeroes out the current headshot timer and points for the player, it also activates the writeup for this player & target (if any). Are you absolutely sure you absolutely sure about this?',
              'method' => 'post',
            ],
          ]);
        },
      ]
    ],
  ],
]);
Pjax::end();
