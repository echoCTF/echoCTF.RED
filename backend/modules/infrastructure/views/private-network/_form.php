<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetwork $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="private-network-form">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($model, 'player_id')->widget(\app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
    'multiple' => false,
    'url' => ['/frontend/player/ajax-search', 'active' => 1, 'status' => 10],
    'options' => ['placeholder' => 'Find player by email, username, id or profile.']
  ])->hint('The player that this entry will belong to.');  ?>


  <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'team_accessible')->checkbox() ?>

  <?= $form->field($model, 'created_at')->textInput() ?>

  <div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>