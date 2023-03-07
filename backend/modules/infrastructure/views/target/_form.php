<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-form">

    <?php $form=ActiveForm::begin([]);?>
    <div class="row">
        <div class="col-md-4">
          <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Unique short name for the target host. Keep in mind that this name is also been used to select the logo for the target on <code>frontend/web/images/target</code>') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'fqdn')->textInput(['maxlength' => true])->hint('Fully Qualified Domain Name for the target host') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'difficulty')->dropDownList([
            0=>"beginner",
            1=>"basic",
            2=>"intermediate",
            3=>"advanced",
            4=>"expert",
            5=>"guru",
            6=>"insane",
          ])->hint('The initial difficulty rating for this target (eg. 0=easy)') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
          <?= $form->field($model, 'category')->textInput()->hint('Target category eg. web') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'status')->dropDownList($model->statuses)->hint('The status setting for this target (eg. powerup)') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'scheduled_at')->textInput()->hint('The Date and time associated with status') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-2">
          <?= $form->field($model, 'active')->checkbox()->hint('Whether the target is active or not (if this is not checked, the target will NOT be available to the players)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'healthcheck')->checkbox()->hint('Whether the target should be checked for health status') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'rootable')->checkbox()->hint('Whether the target is rootable or not (checked=rootable)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'timer')->checkbox()->hint('Should we use timer for this target headshots? (checked=enable)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'writeup_allowed')->checkbox()->hint('Whether or not writeups are allowed for the target (checked=allowed)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'instance_allowed')->checkbox()->hint('Whether or not private instances are allowed for the target (checked=allowed)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'require_findings')->checkbox()->hint('Whether or not findings are required before claiming flags. <small>This value is bypassed by <code>force_findings_to_claim</code> sysconfig key.</small>(checked=required)') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'player_spin')->checkbox()->hint('Allow players to spin targets?') ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'headshot_spin')->checkbox()->hint('Automatic spin target on headshot?') ?>
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
        <div class="col-md-4">
          <?= $form->field($model, 'server')->textInput(['maxlength' => true])->hint('Docker host connection string (tcp://1.2.3.4:1234)') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'image')->textInput(['maxlength' => true])->hint('Image to pull and run') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'imageparams')->textarea(['placeholder'=>'{ "username": "jdoe", "password": "secret", "email": "jdoe@acme.com"}'])->hint('Image registry json parameters') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'weight')->textInput(['maxlength' => true])->hint('Enter a number to be used as weight for ordering') ?>
        </div>
    </div>

    <?= $form->field($model, 'parameters')->textarea(['rows' => 6])->hint('Add extra docker parameters as json object (eg. <code>{"hostConfig":{"Memory":"512"}}</code>)') ?>


    <?php // $form->field($model, 'parameters')->textInput(['maxlength' => true])->hint('Command line parameters for the target') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['name'=>'save', 'class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and destroy', ['name'=>'destroy', 'class' => 'btn btn-danger']) ?>

    </div>

    <?php ActiveForm::end();?>

</div>
