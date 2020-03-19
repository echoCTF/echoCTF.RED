<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-form">

    <?php $form = ActiveForm::begin([]); ?>
    <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Unique short name for the target host') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'fqdn')->textInput(['maxlength' => true])->hint('Fully Qualified Domain Name for the target host') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'status')->dropDownList($model->statuses)->hint('The status setting for this target (eg. powerup)') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'scheduled_at')->textInput()->hint('The Date and time associated with status') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
          <?= $form->field($model, 'difficulty')->textInput()->hint('The initial difficulty rating for this target (eg. 0=easy)') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'active')->checkbox()->hint('Whether the target is active or not (if this is not checked, the target will NOT be available to the players)') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'rootable')->checkbox()->hint('Whether the target is rootable or not (checked=rootable)') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'suggested_xp')->textInput(['maxlength' => true])->hint('Suggested XP the participants need to have to be able to complete the target') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'required_xp')->textInput(['maxlength' => true])->hint('Required XP by the participants to access the target (not enforced at the moment).') ?>
        </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <?= $form->field($model, 'purpose')->textInput(['maxlength' => true])->hint('Any kind of technical details about the target in order to help other moderators/admins understand its purpose') ?>
      </div>
      <div class="col-md-12">
        <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A description of the target (possibly including rationale, type of service(s) etc.)') ?>
      </div>
    </div>


    <div class="row">
        <div class="col-md-3">
          <?= $form->field($model, 'ipoctet')->textInput()->hint('The IP address of the target')->label('IP Address') ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'mac')->textInput(['maxlength' => true])->hint('The MAC address of the target') ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'dns')->textInput(['maxlength' => true])->hint('DNS for the host') ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'net')->textInput(['maxlength' => true])->hint('Docker host network name (eg. AAnet)') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'server')->textInput(['maxlength' => true])->hint('Docker host connection string (tcp://1.2.3.4:1234)') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'image')->textInput(['maxlength' => true])->hint('Image to pull and run') ?>
        </div>
    </div>
    <?= $form->field($model, 'parameters')->textarea(['rows' => 6])->hint('Add extra docker parameters as json object (eg. <code>{"hostConfig":{"Memory":"512"}}</code>)') ?>


    <?php // $form->field($model, 'parameters')->textInput(['maxlength' => true])->hint('Command line parameters for the target') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['name'=>'save','class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and destroy', ['name'=>'destroy','class' => 'btn btn-danger']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
