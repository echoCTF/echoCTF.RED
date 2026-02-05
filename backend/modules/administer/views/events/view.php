<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = "View event {$model->Name}";
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">
  <h1><?= Html::encode($this->title) ?></h1>
  <p>
    <?= Html::a('Update', ['update', 'name' => $model->Name], ['class' => 'btn btn-primary']); ?>
    <?= Html::a('Delete', ['delete', 'name' => $model->Name], [
      'class' => 'btn btn-danger',
      'data-method' => 'post',
      'data-confirm' => 'Are you sure?',
    ]); ?>
  </p>


  <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
      'Name',
      'Status',
      'Time_zone',
      'Execute_at',
      'Starts',
      'Ends',
      'Interval_value',
      'Interval_field',
      'Event_comment'
    ],
  ]); ?>

  <div>
    <h3>Event SQL Code</h3>
    <?= Yii::$app->formatter->asMarkdown('```sql' . "\n" . $eventCode . "\n" . '```'); ?>
  </div>
</div>