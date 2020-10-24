<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */

$this->title='Configure System';
$this->params['breadcrumbs'][]=['label' => 'Sysconfigs', 'url' => ['configure']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="sysconfig-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="sysconfig-form">

        <?php $form=ActiveForm::begin([]);?>

        <?= $form->field($model, 'event_name')->textInput(['maxlength' => true])->hint('Enter the event name') ?>
        <?= $form->field($model, 'footer_logos')->textarea()->hint('Raw html to be placed at the footer of the pUI pages') ?>
        <hr/>
        <h4>Gameplay Scenarios</h4>
          <?= $form->field($model, 'frontpage_scenario')->textarea()->hint('Raw html to be shown on pUI frontpage for guests') ?>
          <?= $form->field($model, 'offense_scenario')->textarea()->hint('Raw html to be shown on pUI for offense participants') ?>
          <?= $form->field($model, 'defense_scenario')->textarea()->hint('Raw html to be shown on pUI for defense participants') ?>
        <hr/>
<!--        <h4>Team properties</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'teams')->checkbox()->hint('Are teams supported?') ?></div>
        </div>
        <hr/>-->
        <h4>Registration and Player properties</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'require_activation')->checkbox()->hint('Do players need to activate their account?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'disable_registration')->checkbox()->hint('Are online registrations allowed on the pUI?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'player_profile')->checkbox()->hint('Are player profiles active?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'profile_visibility')->textInput()->hint('Choose default profile visibility (<code>ingame, public, private</code>)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'default_homepage')->textInput()->hint('Default homepage for logged in users (eg. /dashboard)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'online_timeout')->textInput()->hint('Timeout (in seconds) for the <b><code>online</code></b> memcache key to expire (eg. 900)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'spins_per_day')->textInput()->hint('Maximum target spins per day per user (eg. 2)') ?></div>
        </div>
        <hr/>
        <h4>Generic Settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'vpngw')->textInput(['maxlength' => true])->hint('VPN Gateway FQDN or IP (eg. vpn.echoctf.red)') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'mail_from')->textInput(['maxlength' => true])->hint('Mail From (eg. dontreply@echoctf.red)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_fromName')->textInput(['maxlength' => true])->hint('Mail From Name (eg. echoCTF RED)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_host')->textInput(['maxlength' => true])->hint('Mail host (eg. smtp-relay.gmail.com)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_port')->textInput(['maxlength' => true])->hint('Mail port (eg. 25)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_username')->textInput(['maxlength' => true])->hint('Mail server username') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_password')->textInput(['maxlength' => true])->hint('Mail server password') ?></div>
        </div>
        <hr/>

        <h4>Offense/Defense settings</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'offense_registered_tag')->textInput(['maxlength' => true])->hint('Offense PF/BRIDGE tag for registered players (OFFENSE_REGISTERED)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'defense_registered_tag')->textInput(['maxlength' => true])->hint('Defense PF/BRIDGE tag for registered players (DEFENSE_REGISTERED)') ?></div>
        </div>
        <hr/>



        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end();?>

    </div>

</div>
