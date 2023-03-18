<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'score-monthlyPJ','enablePushState'=>false,'enableReplaceState'=>false,]); ?>
<h5>Monthly Scores</h5>
<?= GridView::widget([
  'id'=>'score-monthly',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    'points',
    'dated_at',
    [
      'attribute'=>'ts',
      'format'=>'html',
      'value'=>function($model) { return sprintf('<abbr title="%s">%s</abbr>',Yii::$app->formatter->asRelativeTime($model->ts),$model->ts);}
    ],
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{delete}',
      'urlCreator' => function ($action, $model, $key, $index) {
          return Url::to(['/activity/player-score-monthly/'.$action, 'player_id' => $model->player_id,'dated_at'=>$model->dated_at]);
      }
    ],
  ],
]); ?>
<?php Pjax::end(); ?>
