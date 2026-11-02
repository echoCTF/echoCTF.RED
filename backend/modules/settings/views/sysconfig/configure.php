<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */

$this->title = 'Configure System';
$this->params['breadcrumbs'][] = ['label' => 'Sysconfigs', 'url' => ['configure']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sysconfig-create">
  <h1><?= Html::encode($this->title) ?></h1>

  <div class="sysconfig-form">

    <?php $form = ActiveForm::begin(['id' => 'configureForm']); ?>

    <h4>Event/CTF Settings</h4>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'event_active')->checkbox()->hint('Is the site active?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'hide_timezone')->checkbox()->hint('Hide timezone from frontend?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'event_name')->textInput(['maxlength' => true])->input('text', ['placeholder' => "My Awesome CTF"])->hint('Enter your event or site name') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'site_description')->textInput(['maxlength' => true])->input('text', ['placeholder' => "My Awesome site description"])->hint('Enter your a description for your site') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'time_zone')->dropDownList(ArrayHelper::map(DateTimeZone::listIdentifiers(), function ($model) {
                              return $model;
                            }, function ($model) {
                              return $model;
                            }))->hint('Choose your timezone') ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'event_start')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the event start date and time') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'event_end')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the event end date and time') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'registrations_start')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the registration start date and time') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'registrations_end')->textInput(['maxlength' => true])->input('text', ['placeholder' => "Y-m-d H:i:s"])->hint('Enter the registration end date and time') ?></div>
    </div>
    <hr />

    <h4>Leaderboard Settings</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'guest_visible_leaderboards')->checkbox()->hint('Allow guests to view the leaderboards?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_before_event_start')->checkbox()->hint('Is the leaderboard going to be visible before the event starts?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'leaderboard_visible_after_event_end')->checkbox()->hint('Is the leaderboard going to be visible after the event ends?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'leaderboard_show_zero')->checkbox()->hint('Leaderboard show zero points?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'player_point_rankings')->checkbox()->hint('Show individual player leaderboards?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'player_monthly_rankings')->checkbox()->hint('Show monthly leaderboards by points?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'country_rankings')->checkbox()->hint('Show country based leaderboards?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'team_only_leaderboards')->checkbox()->hint('Show only team based leaderboards?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'writeup_rankings')->checkbox()->hint('Enable writeup rankings?') ?></div>
    </div>
    <hr />

    <h4>Team properties</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'teams')->checkbox()->hint('Are teams supported?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'team_required')->checkbox()->hint('Are teams required?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'team_encrypted_claims_allowed')->checkbox()->hint('Allow team members to claim each others flags? Only when per player flags are enabled.') ?></div>

      <div class="col-sm-3"><?= $form->field($model, 'team_visible_instances')->checkbox()->hint('Make private instances accessible by team members?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'team_manage_members')->checkbox()->hint('Allow team members management operations?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'members_per_team')->textInput(['maxlength' => true])->hint('How many members are allowed per team (including the team owner)?') ?></div>
    </div>
    <hr />

    <h4>Targets and Challenges properties</h4>
    <div class="row form-group">
      <div class="row">
        <div class="col-sm-2"><?= $form->field($model, 'target_hide_inactive')->checkbox()->hint('Hide inactive targets from listings?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'target_guest_view_deny')->checkbox()->hint('Hide targets from guests?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'network_view_guest')->checkbox()->hint('Allow guests to view networks?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'stream_player_target_help')->checkbox()->hint('Log writeup activations on activity stream? Required for paid activations (<code>writeup_cost_percent</code>)') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'log_failed_claims')->checkbox()->hint('Log failed treasure claims?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'force_findings_to_claim')->checkbox()->hint('Force findings before claim?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'disable_ondemand_operations')->checkbox()->hint('Disable on-demand target operations?') ?></div>
        <div class="col-sm-2"><?= $form->field($model, 'instances_only')->checkbox()->hint('Disable on-demand targets and let every player (not only VIP) spawn private instances. Spin limits still apply.') ?></div>
      </div>
      <div class="col-sm-2"><?= $form->field($model, 'target_restart_log_interval')->textInput(['placeholder'=>'1 hour'])->hint('Interval of restart logs to show. Can take intervals supported by SQL <code>INTERVAL</code>, eg. <code>10 day</code>, meaning 10 days from now') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'challenge_home')->textInput()->hint('Web accessible path for downloading challenge files?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'challenge_root')->textInput()->hint('Folder that challenge files will be uploaded to?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'treasure_secret_key')->textInput()->hint('Secret key to encrypt player flags?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'target_days_new')->textInput()->hint('How many days are targets considered new?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'target_days_updated')->textInput()->hint('How many days are targets considered updated?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'failed_claim_lookup_timeout')->textInput(['placeholder'=>'1'])->hint('Seconds to look up the owner of a wrong flag before it is queued for the background scan (needs <code>log_failed_claims</code>)') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'failed_claim_scan_players')->textInput(['placeholder'=>'1000'])->hint('Players checked per minute for each queued wrong flag by the <code>ev_scan_failed_claims</code> event') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'plus_writeups')->textInput(['placeholder'=>'0'])->hint('Writeup activations allowed on top of the player headshots') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'writeups_use_require_xp')->checkbox()->hint('Require the player score to be at least the target <code>required_xp</code> to activate writeups?') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'writeup_stuck_hours')->textInput(['placeholder'=>'0'])->hint('Hours since the last own flag or finding on a target before writeups can be activated. Needs at least one. Empty or <code>0</code> to disable') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'writeup_cost_percent')->textInput(['placeholder'=>'0'])->hint('Percent of unclaimed target points charged to activate writeups (score must be at least <code>writeup_cost_multiplier</code> times the cost). Remaining flags keep full points. Empty or <code>0</code>, or <code>stream_player_target_help</code> off, keeps the 50% halving') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'writeup_cost_multiplier')->textInput(['placeholder'=>'3'])->hint('Player score must be at least this many times the writeup activation cost. Empty means <code>3</code>') ?></div>
    </div>

    <hr />
    <h4>Player properties</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'player_require_approval')->checkbox()->hint('Do players need to be approved?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'player_require_identification')->checkbox()->hint('Do players need to provide identification proof?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'all_players_vip')->checkbox()->hint('Treat all players as VIP?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'vpn_ip_limit')->textInput(['placeholder'=>'0'])->hint('Maximum number of players connected to the VPN from the same IP address. Leave empty or <code>0</code> to disable') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'require_activation')->checkbox()->hint('Do players need to activate their account?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'disable_registration')->checkbox()->hint('Are online registrations allowed on the pUI?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'approved_avatar')->checkbox()->hint('Are player profile avatars approved?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'player_request_delete_allow')->checkbox()->hint('Allow players to request account deletion?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'signup_StopForumSpamValidator')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'signup_HourRegistrationValidator')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'signup_TotalRegistrationsValidator')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'signup_MXServersValidator')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'signup_ValidatemailValidator')->checkbox()->hint('Enable verifymail.io domain validation?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'verifymail_key')->textInput()->hint('Add API key for verifymail.io') ?></div>
      <div class="row">
        <div class="col-sm-3"><?= $form->field($model, 'username_length_min')->textInput()->hint('Minimum player username length') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'username_length_max')->textInput()->hint('Maximum player username length') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'mail_verification_token_validity')->textInput()->hint('How long will the mail verification tokens be active for. Can take intervals supported by php and <code>INTERVAL</code>, eg. <code>10 day</code>, meaning 10 days from now') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'password_reset_token_validity')->textInput()->hint('How long will the password reset tokens be active for. Can take intervals supported by php and <code>INTERVAL</code>, eg. <code>10 day</code>, meaning 10 days from now') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'online_timeout')->textInput()->hint('Timeout (in seconds) for the <b><code>online</code></b> memcache key to expire (eg. 900)') ?></div>

        <div class="col-sm-3"><?= $form->field($model, 'player_delete_rejected_after')->textInput()->hint('Delete players that their registration was rejected (status=9 and approval=4) after X days. Can take intervals supported by SQL <code>INTERVAL</code>, eg. <code>10 day</code>, meaning after 10 days') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'player_delete_inactive_after')->textInput()->hint('Delete players with status=9 (inactive) after X days. Can take intervals supported by SQL <code>INTERVAL</code>, eg. <code>10 day</code>, meaning after 10 days') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'player_delete_deleted_after')->textInput()->hint('Delete players with status=0 (deleted) after X days. Can take intervals supported by SQL <code>INTERVAL</code>, eg. <code>10 day</code>, meaning after 10 days') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'player_changed_to_deleted_after')->textInput()->hint('Update players with status=8 (changed) into status=0 (deleted) after X days. Can take intervals supported by SQL <code>INTERVAL</code>, eg. <code>10 day</code>, meaning after 10 days') ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3"><?= $form->field($model, 'academic_grouping')->textInput()->hint('Number of participant groups') ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'admin_ids')->textInput()->hint('Comma separated list of player IDs') ?></div>

      </div>
    </div>
    <hr />

    <h4>Profile properties</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'default_homepage')->textInput()->hint('Default homepage for logged in users (eg. /dashboard)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'profile_visibility')->textInput()->hint('Choose default profile visibility (<code>ingame, public, private</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'spins_per_day')->textInput()->hint('Maximum target spins per day per user (eg. 2)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'max_target_instances')->textInput()->hint('Concurrent private target instances new players start with, each for a different target (default 1). Change it per player under Player Spins') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'avatar_generator')->textInput()->hint('Change the avatar generator') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'avatar_robohash_set')->textInput()->hint('Set for Robohash images to use') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'profile_card_disabled_actions')->textInput()->hint('Comma separated list of profile card actions that will be disabled. Can be any combination of <code>badge, edit, profileurl, inviteurl, generate-token, copy-token, revoke, disconnect, delete</code>') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'profile_improvements_disabled')->checkbox()->hint('Disable the improvement tab on player profiles?') ?></div>
    </div>
    <hr />

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
    <hr />

    <h4>Domains and Hosts</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'moderator_domain')->textInput(['maxlength' => true])->hint('Moderator domain')->input('text', ['placeholder' => "admin.example.ctf"]) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'offense_domain')->textInput(['maxlength' => true])->hint('Offense domain')->input('text', ['placeholder' => "red.example.ctf"]) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'defense_domain')->textInput(['maxlength' => true])->hint('Defense domain')->input('text', ['placeholder' => "blue.example.ctf"]) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'vpngw')->textInput(['maxlength' => true])->hint('VPN Gateway FQDN or IP (eg. vpn.echoctf.red)')->input('text', ['placeholder' => "vpngw.example.ctf"]) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'offense_home')->textInput(['maxlength' => true])->hint(false)->input('text') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'defense_home')->textInput(['maxlength' => true])->hint(false)->input('text') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'moderator_home')->textInput(['maxlength' => true])->hint(false)->input('text') ?></div>
    </div>
    <hr />

    <h4>Mail Settings</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'disable_mailer')->checkbox()->hint('Disable all mailer operations?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'disable_mail_validation')->checkbox()->hint('Disable mail validations?') ?></div>

      <div class="col-sm-3"><?= $form->field($model, 'mail_useFileTransport')->checkbox()->hint('Activate the use of file transport (save mails in files)?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'mail_from')->textInput(['maxlength' => true])->hint('Mail From (eg. dontreply@echoctf.red)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'mail_fromName')->textInput(['maxlength' => true])->hint('Mail From Name (eg. echoCTF RED)') ?></div>
      <div class="col-sm-6"><?= $form->field($model, 'dsn')->textInput(['maxlength' => true, 'placeholder' => 'smtp://username:password@mail.example.com:25?local_domain=blah'])->hint('Mail DSN see ' . Html::a('Symphony Mailer', 'https://symfony.com/doc/current/mailer.html', ['title' => 'Symphony Mailer Reference', 'target' => '_blank'])) ?></div>
    </div>
    <hr />

    <h4>Social media Settings</h4>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'twitter_account')->textInput(['maxlength' => true])->hint('Twitter account to use for tagging and via') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'twitter_hashtags')->textInput(['maxlength' => true])->hint('Twitter hashtags to use for tweets') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'disable_stream_tweet')->checkbox()->hint('Disable tweeting individual stream entries') ?></div>
      <div class="col-sm-5"><?= $form->field($model, 'discord_news_webhook')->textInput(['maxlength' => true])->hint('Discord Webhook URL to post platform news and updates') ?></div>
    </div>
    <hr />

    <h4>Platform Settings</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'module_smartcity_disabled')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'module_speedprogramming_enabled')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'dashboard_graph_visible')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'dashboard_news_total_pages')->textInput(['maxlength' => true])->hint('Total pages to return for dashboard news') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'dashboard_news_records_per_page')->textInput(['maxlength' => true])->hint('Records per page for dashbaord news') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'force_https_urls')->checkbox()->hint(false) ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'offense_csp_header')->textInput(['maxlength' => true])->hint('Offense CSP header (eg <code>Content-Security-Policy-Report-Only</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'offense_csp')->textInput(['maxlength' => true])->hint('Offense CSP value') ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'moderator_csp_header')->textInput(['maxlength' => true])->hint('Moderator CSP header (eg <code>Content-Security-Policy-Report-Only</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'moderator_csp')->textInput(['maxlength' => true])->hint('Moderator CSP value') ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'failed_login_ip')->textInput(['maxlength' => true])->hint('Failed logins per IP before denied access (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'failed_login_ip_timeout')->textInput(['maxlength' => true])->hint('Failed logins per IP timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'failed_login_username_timeout')->textInput(['maxlength' => true])->hint('Failed logins per username timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'password_reset_ip')->textInput(['maxlength' => true])->hint('Password reset per IP before denied access (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'password_reset_ip_timeout')->textInput(['maxlength' => true])->hint('Password reset per IP timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'password_reset_email_timeout')->textInput(['maxlength' => true])->hint('Password reset per email timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'verification_resend_ip')->textInput(['maxlength' => true])->hint('Verification resend per IP before denied access (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'verification_resend_ip_timeout')->textInput(['maxlength' => true])->hint('Verification resend per IP timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'verification_resend_email_timeout')->textInput(['maxlength' => true])->hint('Verification resend per email timeout (disable: <code>0</code>)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'stream_record_limit')->textInput(['maxlength' => true])->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'rate_limit_requests')->textInput(['maxlength' => true])->hint("Rate limit number of requests") ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'rate_limit_window')->textInput(['maxlength' => true])->hint("Rate limit window in seconds for the requests") ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'event_finished_path')->textInput(['maxlength' => true])->hint("The path for the file to create when the event is finished") ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'maintenance')->checkbox()->hint('Is the site under maintenance?') ?></div>
      <div class="col-sm-4"><?= $form->field($model, 'maintenance_notification')->textInput(['maxlength' => true])->input('text')->hint('The notification to sent to users when the is under maintenance') ?></div>
    </div>
    <hr />

    <h4>API Settings</h4>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'api_bearer_enable')->checkbox()->hint(false) ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'api_claim_timeout')->textInput(['maxlength' => true])->hint('set the rate limit for the api claim. One request per <code>api_claim_timeout+1</code> seconds') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'api_target_instances_timeout')->textInput(['maxlength' => true])->hint('set the rate limit for the target instances endpoint. One request per <code>api_target_instances_timeout+1</code> seconds') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'api_target_spin_timeout')->textInput(['maxlength' => true])->hint('set the rate limit for the given target operation endpoints. One request per <code>api_target_spin_timeout+1</code> seconds') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'api_target_spawn_timeout')->textInput(['maxlength' => true])->hint('set the rate limit for the given target operation endpoints. One request per <code>api_target_spawn_timeout+1</code> seconds') ?></div>
    </div>
    <hr />

    <h4>Stripe Settings</h4>
    <div class="row form-group">
      <div class="col-sm-1"><?= $form->field($model, 'subscriptions_menu_show')->checkbox()->hint(false) ?></div>
      <div class="col-sm-1"><?= $form->field($model, 'subscriptions_emergency_suspend')->checkbox()->hint(false) ?></div>
      <div class="col-sm-1"><?= $form->field($model, 'subscriptions_feature_api')->checkbox()->hint("Enable API access through subscription") ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'stripe_apiKey')->textInput(['maxlength' => true])->hint('Your Stripe secret API Key') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'stripe_publicApiKey')->textInput(['maxlength' => true])->hint('Your Stripe Public API Key') ?></div>
      <div class="col-sm-4"><?= $form->field($model, 'stripe_webhookSecret')->textInput(['maxlength' => true])->hint('Stripe secret to validate webhook requests') ?></div>
      <div class="col-sm-4"><?= $form->field($model, 'stripe_webhookLocalEndpoint')->textInput(['maxlength' => true, 'placeholder' => 'subscriptions/somethingrandom/webhook'])->hint('Local endpoint url that receives webhook POST from Stripe') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'sales_email')->textInput(['maxlength' => true, 'placeholder' => 'sales@example.com'])->hint('Address the <b>Contact us</b> button of <code>contact_only</code> products emails') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'stripe_url_prefix')->textInput(['maxlength' => true])->hint('An optional prefix to use (eg <code>test</code> for test mode urls') ?></div>
    </div>
    <hr />

    <h4>Firewalling Settings</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'offense_registered_tag')->textInput(['maxlength' => true])->hint('Offense PF/BRIDGE tag for registered players (OFFENSE_REGISTERED)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'defense_registered_tag')->textInput(['maxlength' => true])->hint('Defense PF/BRIDGE tag for registered players (DEFENSE_REGISTERED)') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'pflog_min')->textInput(['maxlength' => true])->hint('Minimum pflog interface to use (<code>default: 0</code> means use <code>pflog0</code>') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'pflog_max')->textInput(['maxlength' => true])->hint('Maximum pflog interface to use (<code>default: 0</code> means use <code>pflog0</code>') ?></div>
      <div class="col-sm-6"><?= $form->field($model, 'pf_state_limits')->textInput(['maxlength' => true])->hint('PF firewall limits imposed to player connections to the targets') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'bannedIPS')->textInput(['maxlength' => true])->hint('List of comma separated player IPs that will be blocked from the platform') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'trusted_proxies')->textInput(['maxlength' => true, 'placeholder' => '10.0.0.0/8,192.0.2.10'])->hint('Comma separated IPs and CIDR ranges of the reverse proxies or CDNs in front of the frontend. Leave empty when players connect directly') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'proxy_ip_header')->textInput(['maxlength' => true, 'placeholder' => 'X-Forwarded-For'])->hint('Header that carries the player IP when <code>trusted_proxies</code> is set, eg. <code>CF-Connecting-IP</code> behind Cloudflare') ?></div>
    </div>
    <hr />

    <h4>VPN Certificate Settings</h4>
    <small class="text-danger">If you change these values you will have to regenerate your CA keys and player certificates.</small>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'dn_countryName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'dn_stateOrProvinceName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'dn_localityName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'dn_organizationName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'dn_organizationalUnitName')->textInput(['maxlength' => true])->input('text', ['placeholder' => ""])->hint('') ?></div>
    </div>
    <hr />


    <div class="form-group">
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

  </div>

</div>
