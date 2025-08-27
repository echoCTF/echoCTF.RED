<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>

<?php Pjax::begin(['id' => 'stream-lagPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Stream lag</h5>
<?= GridView::widget([
  'id'=>'stream-lag',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'showHeader'=> false,
  'tableOptions' => ['class' => 'table table-hover'],
  'columns' => [
    [
      'attribute'=>'formatted',
      'format'=>'html',
      'headerOptions' => ['style' => 'width:95%'],
      'value'=>function ($model){
        if($model->seconds_since_last)
          return sprintf('<small>%s <sub><abbr title="%s after previous record">%s</abbr></sub></small>',$model->formatted,Yii::$app->formatter->asDuration($model->seconds_since_last),$model->ts_ago);
        else
          return "<small>". $model->formatted. " <sub>". $model->ts_ago. "</sub></small>";
      }
    ],
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{delete}',
      'urlCreator' => function ($action, $model, $key, $index) {
        return Url::to(['/activity/stream/' . $action, 'id' => $model->id]);
      }
    ],
  ],
]); ?>

<?php Pjax::end(); ?>