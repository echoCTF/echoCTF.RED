<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\NetworkTargetScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Pjax::begin(['id' => 'network-schedule-tabPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>

<h1>Network Target Schedule</h1>

<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'id' => 'network-schedule-tab',
  'columns' => [
    [
      'attribute' => 'target_name',
      'value' => 'target.name',
    ],
    [
      'attribute' => 'network_name',
      'value' => 'network.name',
    ],
    'migration_date',

    ['class' => 'yii\grid\ActionColumn'],
  ],
]);
Pjax::end();
