<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<?php Pjax::begin(['id' => 'writeup-ratingPJ','enablePushState'=>false,'enableReplaceState'=>false,]);?>
<h5>Writeup Ratings</h5>
<?= GridView::widget([
  'id'=>'writeup-rating',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [

    'id',
    [
      'attribute' => 'writeup_id',
      'value' => function ($model) {
        return sprintf("%d: %s - %s", $model->writeup_id, $model->writeup->player->username, $model->writeup->target->name);
      }
    ],
    'rating',
    'created_at',
  ],
]); ?>
<?php Pjax::end(); ?>
