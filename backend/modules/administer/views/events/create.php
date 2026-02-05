<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Create Event';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="event-create">

  <h1><?= Html::encode($this->title) ?></h1>


  <div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Event_comment')->textarea([
      'rows' => 20,
      'style' => 'font-family: monospace;',
    ])->label('Event code'); ?>

    <div class="form-group">
      <?= Html::submitButton('Create Event', ['class' => 'btn btn-success']); ?>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>