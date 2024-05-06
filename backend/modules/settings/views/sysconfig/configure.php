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
          <div class="col-sm-2"><?= $form->field($model, 'event_active')->checkbox()->hint('Is the site active?') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'hide_timezone')->checkbox()->hint('Hide timezone from frontend?') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'event_name')->textInput(['maxlength' => true])->input('text', ['placeholder' => "My Awesome CTF"])->hint('Enter your event or site name') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'site_description')->textInput(['maxlength' => true])->input('text', ['placeholder' => "My Awesome site description"])->hint('Enter your a description for your site') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'time_zone')->dropDownList(ArrayHelper::map(DateTimeZone::listIdentifiers(), function($model){ return $model;},function($model){ return $model;}))->hint('Choose your timezone') ?></div>
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
          <div class="col-sm-2"><?= $form->field($model, 'guest_visible_leaderboards')->checkbox()->hint('Allow guests to view the leaderboards?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_before_event_start')->checkbox()->hint('Is the leaderboard going to be visible before the event starts?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_after_event_end')   ->checkbox()->hint('Is the leaderboard going to be visible after the event ends?') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'leaderboard_show_zero')   ->checkbox()->hint('Leaderboard show zero points?') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'monthly_leaderboards')   ->checkbox()->hint('Show monthly leaderboards by points?') ?></div>
        </div>
        <hr/>
        <h4>Team properties</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'teams')->checkbox()->hint('Are teams supported?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'team_required')->checkbox()->hint('Are teams required?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'team_visible_instances')->checkbox()->hint('Make private instances accessible by team members?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'team_manage_members')->checkbox()->hint('Allow team members management operations?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'members_per_team')->textInput(['maxlength'=>true])->hint('How many members are allowed per team (including the team owner)?') ?></div>
        </div>
        <hr/>
        <h4>Targets and Challenges properties</h4>
        <div class="row form-group">

          <div class="col-sm-3"><?= $form->field($model, 'target_hide_inactive')->checkbox()->hint('Hide inactive targets from listings?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'target_guest_view_deny')->checkbox()->hint('Hide targets from guests?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'network_view_guest')->checkbox()->hint('Allow guests to view networks?') ?></div>

          <div class="col-sm-3"><?= $form->field($model, 'target_days_new')->textInput()->hint('How many days are targets considered new?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'target_days_updated')->textInput()->hint('How many days are targets considered updated?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'challenge_home')->textInput()->hint('Web accessible path for downloading challenge files?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'challenge_root')->textInput()->hint('Folder that challenge files will be uploaded to?') ?></div>

        </div>

        <hr/>
        <h4>Player Registration properties</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'player_require_approval')->checkbox()->hint('Do players need to be approved?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'player_require_identification')->checkbox()->hint('Do players need to provide identification proof?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'all_players_vip')->checkbox()->hint('Treat all players as VIP?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'require_activation')->checkbox()->hint('Do players need to activate their account?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'disable_registration')->checkbox()->hint('Are online registrations allowed on the pUI?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'approved_avatar')->checkbox()->hint('Are player profile avatars approved?') ?></div>
        </div>
        <hr/>

        <h4>Profile social details</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'profile_visibility')->textInput()->hint('Choose default profile visibility (<code>ingame, public, private</code>)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'default_homepage')->textInput()->hint('Default homepage for logged in users (eg. /dashboard)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'online_timeout')->textInput()->hint('Timeout (in seconds) for the <b><code>online</code></b> memcache key to expire (eg. 900)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'spins_per_day')->textInput()->hint('Maximum target spins per day per user (eg. 2)') ?></div>
        </div>
        <hr/>

        <h4>Player social details</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'player_profile')->checkbox()->hint('Are player profiles active?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_discord')->checkbox()->hint('Player Discord details?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_echoctf')->checkbox()->hint('Player echoCTF profile') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_twitter')->checkbox()->hint('Player Twitter/X profile') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_github')->checkbox()->hint('Player Github profile') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_htb')->checkbox()->hint('Player HTB profile') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_twitch')->checkbox()->hint('Player Twitch profile') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'profile_youtube')->checkbox()->hint('Player Youtube profile') ?></div>
        </div>
        <hr/>

        <h4>Domains and Hosts</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'moderator_domain')->textInput(['maxlength' => true])->hint('Moderator domain')->input('text', ['placeholder' => "admin.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'offense_domain')->textInput(['maxlength' => true])->hint('Offense domain')->input('text', ['placeholder' => "red.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'defense_domain')->textInput(['maxlength' => true])->hint('Defense domain')->input('text', ['placeholder' => "blue.example.ctf"]) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'vpngw')->textInput(['maxlength' => true])->hint('VPN Gateway FQDN or IP (eg. vpn.echoctf.red)')->input('text', ['placeholder' => "vpngw.example.ctf"]) ?></div>
        </div>
        <hr/>

        <h4>Mail Settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'mail_useFileTransport')->checkbox()->hint('Activate the use of file transport (save mails in files)?') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_from')->textInput(['maxlength' => true])->hint('Mail From (eg. dontreply@echoctf.red)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'mail_fromName')->textInput(['maxlength' => true])->hint('Mail From Name (eg. echoCTF RED)') ?></div>
          <div class="col-sm-6"><?= $form->field($model, 'dsn')->textInput(['maxlength' => true,'placeholder'=>'smtp://username:password@mail.example.com:25?local_domain=blah'])->hint('Mail DSN see '.Html::a('Symphony Mailer','https://symfony.com/doc/current/mailer.html',['title'=>'Symphony Mailer Reference','target'=>'_blank'])) ?></div>
        </div>
        <hr/>

        <h4>Social media Settings</h4>
        <div class="row form-group">
          <div class="col-sm-2"><?= $form->field($model, 'twitter_account')->textInput(['maxlength' => true])->hint('Twitter account to use for tagging and via') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'twitter_hashtags')->textInput(['maxlength' => true])->hint('Twitter hashtags to use for tweets') ?></div>
          <div class="col-sm-5"><?= $form->field($model, 'discord_news_webhook')->textInput(['maxlength' => true])->hint('Discord Webhook URL to post platform news and updates') ?></div>
        </div>
        <hr/>

        <h4>Offense/Defense settings</h4>
        <div class="row form-group">
          <div class="col-sm-3"><?= $form->field($model, 'offense_registered_tag')->textInput(['maxlength' => true])->hint('Offense PF/BRIDGE tag for registered players (OFFENSE_REGISTERED)') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'defense_registered_tag')->textInput(['maxlength' => true])->hint('Defense PF/BRIDGE tag for registered players (DEFENSE_REGISTERED)') ?></div>
          <div class="col-sm-6"><?= $form->field($model, 'pf_state_limits')->textInput(['maxlength' => true])->hint('PF firewall limits imposed to player connections to the targets') ?></div>
        </div>
        <hr/>

        <h4>Stripe Settings</h4>
        <div class="row form-group">
          <div class="col-sm-1"><?= $form->field($model, 'subscriptions_menu_show')->checkbox()->hint(false) ?></div>
          <div class="col-sm-1"><?= $form->field($model, 'subscriptions_emergency_suspend')->checkbox()->hint(false) ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'stripe_apiKey')->textInput(['maxlength' => true])->hint('Your Stripe secret API Key') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'stripe_publicApiKey')->textInput(['maxlength' => true])->hint('Your Stripe Public API Key') ?></div>
          <div class="col-sm-4"><?= $form->field($model, 'stripe_webhookSecret')->textInput(['maxlength' => true])->hint('Stripe secret to validate webhook requests') ?></div>
        </div>
        <hr/>

        <h4>VPN Certificate Settings</h4>
        <small class="text-danger">If you change these values you will have to regenerate your CA keys and player certificates.</small>

        <div class="row form-group">
          <div class="col-sm-2"><?= $form->field($model, 'dn_countryName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'dn_stateOrProvinceName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'dn_localityName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
          <div class="col-sm-2"><?= $form->field($model, 'dn_organizationName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
          <div class="col-sm-3"><?= $form->field($model, 'dn_organizationalUnitName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
        </div>
        <hr/>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end();?>

    </div>

</div>
