<?php
use yii\widgets\Pjax;
use yii\grid\GridView;
Pjax::begin(['id' => 'spin-historyPJ', 'enablePushState' => false, 'enableReplaceState' => false,]);
echo GridView::widget([
  'id'=>'spin-history',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    'id',
    [
      'attribute' => 'target',
      'label' => 'Target',
      'value' => function ($model) {
        return sprintf("id:%d %s", $model->target_id, $model->target->name);
      },
    ],
    'created_at:dateTime',
    'updated_at:dateTime',

  ],
]);
Pjax::end();
