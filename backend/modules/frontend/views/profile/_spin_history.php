<?php
use yii\widgets\Pjax;
use yii\grid\GridView;
Pjax::begin(['id' => 'spin-historyPJ', 'enablePushState' => false, 'enableReplaceState' => false,]);?>
<h5>Spin History</h5>
<?php echo GridView::widget([
  'id'=>'spin-history',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    'id',
    [
      'attribute' => 'target_name',
      'label' => 'Target',
      'value' => 'target.name',
    ],
    'created_at:dateTime',
  ],
]);
Pjax::end();
