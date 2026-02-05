<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Update event: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'name' => $model->Name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-update">

  <h1><?= Html::encode($this->title) ?></h1>
  <div class="event-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'Name')->textInput(['readonly' => true]); ?>

    <?= $form->field($model, 'Event_comment')->textarea([
      'rows' => 20,
      'style' => 'font-family: monospace;',
    ]); ?>
    <div class="form-group">
      <?= Html::submitButton('Update Event', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>