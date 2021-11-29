<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

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

        <h4>Event/CTF Settings</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'event_name')->textInput(['maxlength' => true])->input('text', ['placeholder' => "My Awesome CTF"])->hint('Enter your event or site name') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'time_zone')->dropDownList(ArrayHelper::map(DateTimeZone::listIdentifiers(), function($model){ return $model;},function($model){ return $model;}))->hint('Enter your timezone') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'event_active')->checkbox()->hint('Is the site active?') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'event_start')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the event start date and time in UTC') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'event_end')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the event end date and time in UTC') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'registrations_start')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the registration start date and time in UTC') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'registrations_end')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the registration end date and time in UTC') ?></div>
        </div>
        <hr/>
        <h4>Leaderboard Settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_before_event_start')->checkbox()->hint('Is the leaderboard going to be visible before the event starts?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_after_event_end')   ->checkbox()->hint('Is the leaderboard going to be visible after the event ends?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'leaderboard_show_zero')   ->checkbox()->hint('Leaderboard show zero points?') ?></div>
        </div>
        <hr/>
        <h4>Platform website texts</h4>
          <?= $form->field($model, 'footer_logos')->textarea(['rows' => '6'])->hint('Raw html to be placed at the footer of the pUI pages') ?>
          <?= $form->field($model, 'frontpage_scenario')->textarea()->hint('Raw html to be shown on pUI frontpage for guests') ?>
          <?= $form->field($model, 'offense_scenario')->textarea()->hint('Raw html to be shown on pUI for offense participants') ?>
          <?= $form->field($model, 'defense_scenario')->textarea()->hint('Raw html to be shown on pUI for defense participants') ?>
        <hr/>
        <h4>Team properties</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'teams')->checkbox()->hint('Are teams supported?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'team_required')->checkbox()->hint('Are teams required?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'team_manage_members')->checkbox()->hint('Allow team members management operations?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'members_per_team')->textInput(['maxlength'=>true])->hint('How many members are allowed per team (including the team owner)?') ?></div>
        </div>
        <hr/>
        <h4>Registration and Player properties</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'require_activation')->checkbox()->hint('Do players need to activate their account?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'disable_registration')->checkbox()->hint('Are online registrations allowed on the pUI?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'player_profile')->checkbox()->hint('Are player profiles active?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'approved_avatar')->checkbox()->hint('Are player profile avatars approved?') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'profile_visibility')->textInput()->hint('Choose default profile visibility (<code>ingame, public, private</code>)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'default_homepage')->textInput()->hint('Default homepage for logged in users (eg. /dashboard)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'online_timeout')->textInput()->hint('Timeout (in seconds) for the <b><code>online</code></b> memcache key to expire (eg. 900)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'spins_per_day')->textInput()->hint('Maximum target spins per day per user (eg. 2)') ?></div>
        </div>
        <hr/>

        <h4>Domains and Hosts</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'moderator_domain')->textInput(['maxlength' => true])->hint('Moderator domain')->input('text', ['placeholder' => "admin.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'offense_domain')->textInput(['maxlength' => true])->hint('Offense domain')->input('text', ['placeholder' => "red.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'defense_domain')->textInput(['maxlength' => true])->hint('Defense domain')->input('text', ['placeholder' => "blue.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'vpngw')->textInput(['maxlength' => true])->hint('VPN Gateway FQDN or IP (eg. vpn.echoctf.red)')->input('text', ['placeholder' => "vpngw.example.ctf"]) ?></div>
        </div>

        <h4>Mail Settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'mail_useFileTransport')->checkbox()->hint('Activate the use of file transport (save mails in files)?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_from')->textInput(['maxlength' => true])->hint('Mail From (eg. dontreply@echoctf.red)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_fromName')->textInput(['maxlength' => true])->hint('Mail From Name (eg. echoCTF RED)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_host')->textInput(['maxlength' => true])->hint('Mail host (eg. smtp-relay.gmail.com)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_port')->textInput(['maxlength' => true])->hint('Mail port (eg. 25)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_username')->textInput(['maxlength' => true])->hint('Mail server username') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_password')->textInput(['maxlength' => true])->hint('Mail server password') ?></div>
        </div>
        <hr/>

        <h4>Twitter Settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'twitter_account')->textInput(['maxlength' => true])->hint('Twitter account to use for tagging and via') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'twitter_hashtags')->textInput(['maxlength' => true])->hint('Twitter hashtags to use for tweets') ?></div>
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
