<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'target-progressPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Target Progress</h5>
<?= GridView::widget([
  'id'=>'target-progress',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    ['class' => 'yii\grid\SerialColumn'],

    'id',
    [
      'attribute' => 'hostname',
      'contentOptions' => ['class' => 'text-nowrap'],
      'value' => 'target.name',
    ],
    'player_treasures',
    'player_findings',
    'player_points',

    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{delete}',
      'urlCreator' => function ($action, $model, $key, $index) {
        return Url::to(['/activity/target-player-state/' . $action, 'id' => $model->id]);
      }
    ],
  ],
]); ?>

<?php Pjax::end(); ?>