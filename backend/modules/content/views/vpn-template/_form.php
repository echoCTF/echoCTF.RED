<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\VpnTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vpn-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-md-3"><?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('A short unique name for this template') ?></div>
      <div class="col-md-3"><?= $form->field($model, 'filename')->textInput(['maxlength' => true])->hint('A filename to be used when downloading') ?></div>
      <div class="col-md-6"><?= $form->field($model, 'description')->textInput(['maxlength'=>true])->hint('An (optional) short description for this template') ?></div>
    </div>

    <div class="row">
      <div class="col-md-3"><?= $form->field($model, 'active')->checkBox()->hint('Whether or not this template is active') ?></div>
      <div class="col-md-3"><?= $form->field($model, 'visible')->checkBox()->hint('Whether or not this template is visible on players') ?></div>
      <div class="col-md-3"><?= $form->field($model, 'client')->checkBox()->hint('Whether or not this template is for vpn clients') ?></div>
      <div class="col-md-3"><?= $form->field($model, 'server')->checkBox()->hint('Whether or not this template is for vpn servers') ?></div>
    </div>

    <?= $form->field($model, 'content')->textarea(['rows' => 12,'style'=>'font-family: monospace'])->hint('The configuration file contents (supports PHP)') ?>
    <hr/>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
