<?php

use yii\grid\GridView;
use yii\helpers\Html;
$this->title = 'Events';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="event-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Event', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'columns' => [
    'Name',
    'Status',
    'Time_zone',
    'Execute_at',
    'Starts',
    'Ends',
    'Interval_value',
    'Interval_field',
    [
      'class' => 'yii\grid\ActionColumn',
      'urlCreator' => function ($action, $model) {
        return [$action, 'name' => $model->Name];
      },
    ],
  ],
]);?>
</div>
