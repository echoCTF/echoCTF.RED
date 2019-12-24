<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */

$this->title = 'Configure System';
$this->params['breadcrumbs'][] = ['label' => 'Sysconfigs', 'url' => ['configure']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sysconfig-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="sysconfig-form">

        <?php $form = ActiveForm::begin([]); ?>

        <?= $form->field($model, 'event_name')->textInput(['maxlength' => true])->hint('Enter the event name') ?>
        <?= $form->field($model, 'footer_logos')->textarea()->hint('Raw html to be placed at the footer of the pUI pages') ?>
        <hr/>
        <h4>Gameplay Scenarios</h4>
          <?= $form->field($model, 'frontpage_scenario')->textarea()->hint('Raw html to be shown on pUI frontpage for guests') ?>
          <?= $form->field($model, 'offense_scenario')->textarea()->hint('Raw html to be shown on pUI for offense participants') ?>
          <?= $form->field($model, 'defense_scenario')->textarea()->hint('Raw html to be shown on pUI for defense participants') ?>
        <hr/>
        <h4>Findings methods</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'trust_user_ip')->checkbox()->hint('Trust the user IP. Used in combination with strict_activation to enforce IP of user matches IP of request.') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'mac_auth')->checkbox()->hint('Enable MAC address validation for Players. Disables trust_user_ip.') ?></div>
        </div>
        <hr/>
        <h4>Team properties</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'teams')->checkbox()->hint('Are teams supported?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'team_manage_members')->checkbox()->hint('Is team management allowed? If not members will only be allowed to register and create a team of their own. No join/cancel etc.') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'join_team_with_token')->checkbox()->hint('Join team with tokens?') ?></div>
        </div>
        <hr/>
        <h4>Registration and Player properties</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'require_activation')->checkbox()->hint('Do players need to activate their account?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'disable_registration')->checkbox()->hint('Are online registrations allowed on the pUI?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'strict_activation')->checkbox()->hint('Enforce strict activation of player accounts based their IP?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'player_profile')->checkbox()->hint('Are player profiles active?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'registerForm_academic')->checkbox()->hint('Registration form ask academic?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'registerForm_fullname')->checkbox()->hint('Registration form ask fullname?') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'dashboard_is_home')->checkbox()->hint('Redirect to dashboard/index instead of site/index') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'online_timeout')->textInput()->hint('Timeout for the <b><code>online</code></b> memcache key to expire (eg. 900)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'spins_per_day')->textInput()->hint('Maximum target spins per day per user (eg. 2)') ?></div>
        </div>
        <hr/>
        <h4>Generic Settings</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'vpngw')->textInput(['maxlength' => true])->hint('VPN Gateway FQDN or IP (eg. vpn.echoctf.red)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'award_points')->dropDownList(['single'=>'single','full'=>'full','devider'=>'devider'])->hint('How are points awarded?') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'mail_from')->textInput(['maxlength' => true])->hint('Mail From (eg. dontreply@echoctf.red)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_fromName')->textInput(['maxlength' => true])->hint('Mail From Name (eg. echoCTF RED)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_host')->textInput(['maxlength' => true])->hint('Mail host (eg. smtp-relay.gmail.com)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_port')->textInput(['maxlength' => true])->hint('Mail port (eg. 25)') ?></div>
        </div>
        <hr/>
        <h4>Competition domains</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'offense_domain')->textInput(['maxlength' => true])->hint('Offense domain (eg. echoctf.blue)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'defense_domain')->textInput(['maxlength' => true])->hint('Defense domain (eg. echoctf.red)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'moderator_domain')->textInput(['maxlength' => true])->hint('Moderator domain (eg. echoctf.net)') ?></div>
        </div>
        <hr/>

        <h4>Offense network settings</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'offense_registered_tag')->textInput(['maxlength' => true])->hint('Offense PF/BRIDGE tag for registered players (OFFENSE_REGISTERED)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'offense_bridge_if')->textInput(['maxlength' => true])->hint('Offense bridge interface (eg. bridge0)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'offense_eth_if')->textInput(['maxlength' => true])->hint('Offense ethernet interface name (eg em0)') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'offense_vether_network')->textInput(['maxlength' => true])->hint('Offense network for vether (eg 10.10.0.0)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'offense_vether_netmask')->textInput(['maxlength' => true])->hint('Offense netmask for vether (eg 255.255.0.0)') ?></div>
        </div>
        <hr/>


        <h4>Defense network settings</h4>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'defense_vether_network')->textInput(['maxlength' => true])->hint('Defense network for vether (eg 10.10.0.0)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'defense_vether_netmask')->textInput(['maxlength' => true])->hint('Defense netmask for vether (eg 255.255.0.0)') ?></div>
        </div>
        <div class="row form-group">
          <div class="col-sm-4"><?= $form->field($model, 'defense_registered_tag')->textInput(['maxlength' => true])->hint('Defense PF/BRIDGE tag for registered players (OFFENSE_REGISTERED)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'defense_bridge_if')->textInput(['maxlength' => true])->hint('Defense bridge interface (eg. bridge0)') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'defense_eth_if')->textInput(['maxlength' => true])->hint('Defense ethernet interface name (eg em0)') ?></div>
        </div>
        <hr/>
        <h4>Application folders</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'offense_home')->textInput(['maxlength' => true])->hint('Offense home (eg. /home/echoctf.red)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'defense_home')->textInput(['maxlength' => true])->hint('Deffense home (eg. /home/echoctf.blue)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'moderator_home')->textInput(['maxlength' => true])->hint('Moderator home (eg. /home/echoctf.net)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'challenge_home')->textInput(['maxlength' => true])->hint('Challenges storage folder (eg. upload/)') ?></div>
        </div>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
