<?php

namespace app\modules\settings\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Login form
 */
class ConfigureForm extends Model
{
  public $teams;
  public $team_required;
  public $members_per_team;
  public $team_manage_members;
  public $require_activation;
  public $disable_registration;
  public $approved_avatar;
  public $player_profile;
  public $profile_visibility;
  public $event_name;
  public $site_description;
  public $event_active;
  public $event_start;
  public $event_end;
  public $twitter_account;
  public $twitter_hashtags;
  public $registrations_start;
  public $registrations_end;
  public $challenge_home = '@webroot/uploads';
  public $challenge_root = '@web/uploads';
  public $offense_registered_tag;
  public $defense_registered_tag;
  public $offense_domain;
  public $defense_domain;
  public $moderator_domain;
  public $vpngw;
  public $dashboard_is_home;
  public $default_homepage;
  public $mail_from;
  public $mail_fromName;
  public $mail_host;
  public $mail_port;
  public $mail_username;
  public $mail_password;
  public $mail_encryption;
  public $mail_verify_peer;
  public $mail_verify_peer_name;
  public $mail_useFileTransport;
  public $online_timeout;
  public $spins_per_day;
  public $leaderboard_visible_before_event_start;
  public $leaderboard_visible_after_event_end;
  public $leaderboard_show_zero;
  public $time_zone;
  public $target_days_new = 2;
  public $target_days_updated = 1;
  public $discord_news_webhook;
  public $pf_state_limits;
  public $stripe_apiKey;
  public $stripe_publicApiKey;
  public $stripe_webhookSecret;
  public $stripe_webhookLocalEndpoint;
  public $player_monthly_rankings;
  public $player_point_rankings;
  public $country_rankings;
  public $writeup_rankings;
  public $disable_mailer;
  public $log_failed_claims;
  public $team_encrypted_claims_allowed;
  public $module_smartcity_disabled;
  public $api_bearer_enable;
  public $subscriptions_menu_show;
  public $subscriptions_emergency_suspend;
  public $player_require_approval;
  public $player_require_identification;
  public $all_players_vip;
  public $team_visible_instances;
  public $target_hide_inactive;
  public $target_guest_view_deny;
  public $network_view_guest;
  public $hide_timezone;
  public $profile_discord;
  public $profile_echoctf;
  public $profile_twitter;
  public $profile_github;
  public $profile_htb;
  public $profile_twitch;
  public $profile_youtube;
  public $guest_visible_leaderboards;
  public $dsn;
  public $treasure_secret_key;
  public $api_claim_timeout;
  public $api_target_instances_timeout;
  public $api_target_spin_timeout;
  public $api_target_spawn_timeout;
  public $player_delete_inactive_after;
  public $player_delete_deleted_after;
  public $player_changed_to_deleted_after;
  public $player_delete_rejected_after;
  public $mail_verification_token_validity;
  public $password_reset_token_validity;
  public $dn_countryName;
  public $dn_stateOrProvinceName;
  public $dn_localityName;
  public $dn_organizationName;
  public $dn_organizationalUnitName;
  public $pflog_min, $pflog_max;
  public $dashboard_graph_visible;
  public $disable_ondemand_operations;
  public $maintenance;
  public $maintenance_notification;
  public $force_findings_to_claim;
  public $academic_grouping;
  public $admin_ids;
  public $avatar_generator;
  public $avatar_robohash_set;
  public $disable_mail_validation;
  public $module_speedprogramming_enabled;
  public $force_https_urls;
  public $stream_record_limit;
  public $bannedIPS;
  public $offense_home;
  public $defense_home;
  public $moderator_home;
  public $failed_login_ip;
  public $password_reset_ip;
  public $verification_resend_ip;
  public $signup_ValidatemailValidator;
  public $signup_StopForumSpamValidator;
  public $signup_HourRegistrationValidator;
  public $signup_TotalRegistrationsValidator;
  public $signup_MXServersValidator;
  public $password_reset_email;
  public $verification_resend_email;
  public $username_length_min;
  public $username_length_max;
  public $failed_login_ip_timeout;
  public $failed_login_username_timeout;
  public $password_reset_ip_timeout;
  public $password_reset_email_timeout;
  public $verification_resend_ip_timeout;
  public $verification_resend_email_timeout;
  public $verifymail_key;
  public $team_only_leaderboards;
  public $profile_card_disabled_actions;

  public $keys = [
    'target_days_updated',
    'target_days_new',
    'twitter_account',
    'twitter_hashtags',
    'teams',
    'team_required',
    'team_manage_members',
    'members_per_team',
    'require_activation',
    'approved_avatar',
    'disable_registration',
    'player_profile',
    'profile_visibility',
    'event_name',
    'site_description',
    'event_active',
    'event_start',
    'event_end',
    'registrations_start',
    'registrations_end',
    'challenge_home',
    'challenge_root',
    'offense_registered_tag',
    'defense_registered_tag',
    'vpngw',
    'offense_domain',
    'defense_domain',
    'moderator_domain',
    'dashboard_is_home',
    'default_homepage',
    'mail_from',
    'mail_fromName',
    'mail_host',
    'mail_port',
    'mail_username',
    'mail_password',
    'mail_useFileTransport',
    'mail_encryption',
    'mail_verify_peer',
    'mail_verify_peer_name',
    'online_timeout',
    'spins_per_day',
    'team_manage_members',
    'leaderboard_visible_before_event_start',
    'leaderboard_visible_after_event_end',
    'leaderboard_show_zero',
    'treasure_secret_key',
    'time_zone',
    'dn_countryName',
    'dn_stateOrProvinceName',
    'dn_localityName',
    'dn_organizationName',
    'dn_organizationalUnitName',
    'discord_news_webhook',
    'pf_state_limits',
    'stripe_apiKey',
    'stripe_publicApiKey',
    'stripe_webhookSecret',
    'player_monthly_rankings',
    'player_point_rankings',
    'country_rankings',
    'writeup_rankings',
    'disable_mailer',
    'log_failed_claims',
    'team_encrypted_claims_allowed',
    'module_smartcity_disabled',
    'api_bearer_enable',
    'api_claim_timeout',
    'api_target_instances_timeout',
    'api_target_spin_timeout',
    'api_target_spawn_timeout',
    'player_delete_inactive_after',
    'player_delete_deleted_after',
    'player_changed_to_deleted_after',
    'player_delete_rejected_after',
    'mail_verification_token_validity',
    'password_reset_token_validity',
    'subscriptions_menu_show',
    'subscriptions_emergency_suspend',
    'player_require_approval',
    'player_require_identification',
    'all_players_vip',
    'team_visible_instances',
    'target_hide_inactive',
    'target_guest_view_deny',
    'network_view_guest',
    'hide_timezone',
    'profile_discord',
    'profile_echoctf',
    'profile_twitter',
    'profile_github',
    'profile_htb',
    'profile_twitch',
    'profile_youtube',
    'guest_visible_leaderboards',
    'dsn',
    'pflog_min',
    'pflog_max',
    'stripe_webhookLocalEndpoint',
    'dashboard_graph_visible',
    'disable_ondemand_operations',
    'maintenance',
    'maintenance_notification',
    'force_findings_to_claim',
    'academic_grouping',
    'admin_ids',
    'avatar_generator',
    'avatar_robohash_set',
    'disable_mail_validation',
    'module_speedprogramming_enabled',
    'force_https_urls',
    'stream_record_limit',
    'bannedIPS',
    'offense_home',
    'defense_home',
    'moderator_home',
    'failed_login_ip',
    'password_reset_ip',
    'verification_resend_ip',
    'signup_ValidatemailValidator',
    'signup_StopForumSpamValidator',
    'signup_HourRegistrationValidator',
    'signup_TotalRegistrationsValidator',
    'signup_MXServersValidator',
    'password_reset_email',
    'verification_resend_email',
    'username_length_min',
    'username_length_max',
    'failed_login_ip_timeout',
    'failed_login_username_timeout',
    'password_reset_ip_timeout',
    'password_reset_email_timeout',
    'verification_resend_ip_timeout',
    'verification_resend_email_timeout',
    'verifymail_key',
    'team_only_leaderboards',
    'profile_card_disabled_actions',
  ];

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [[
        'offense_registered_tag',
        'defense_registered_tag',
        'vpngw',
        'mail_from',
        'mail_fromName',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_verify_peer',
        'mail_verify_peer_name',
        'profile_visibility',
        'default_homepage',
        'offense_domain',
        'defense_domain',
        'moderator_domain',
        'twitter_account',
        'twitter_hashtags',
        'discord_news_webhook',
        'time_zone',
        'dn_countryName',
        'dn_stateOrProvinceName',
        'dn_localityName',
        'dn_organizationName',
        'dn_organizationalUnitName',
        'pf_state_limits',
        'stripe_apiKey',
        'stripe_publicApiKey',
        'stripe_webhookSecret',
        'stripe_webhookLocalEndpoint',
        'dsn',
        'treasure_secret_key',
        'mail_verification_token_validity',
        'password_reset_token_validity',
        'stripe_webhookLocalEndpoint',
        'bannedIPS',
        'admin_ids',
        'maintenance_notification',
        'avatar_generator',
        'avatar_robohash_set',
        'offense_home',
        'defense_home',
        'moderator_home',
        'verifymail_key',
        'profile_card_disabled_actions',
      ], 'string'],
      [[
        'offense_registered_tag',
        'defense_registered_tag',
        'vpngw',
        'mail_from',
        'mail_fromName',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'profile_visibility',
        'default_homepage',
        'offense_domain',
        'defense_domain',
        'moderator_domain',
        'site_description',
        'event_start',
        'event_end',
        'registrations_start',
        'registrations_end',
        'twitter_account',
        'twitter_hashtags',
        'discord_news_webhook',
        'time_zone',
        'dn_countryName',
        'dn_stateOrProvinceName',
        'dn_localityName',
        'dn_organizationName',
        'dn_organizationalUnitName',
        'pf_state_limits',
        'dsn',
        'treasure_secret_key',
        'mail_verification_token_validity',
        'password_reset_token_validity',
        'stripe_webhookLocalEndpoint',
        'bannedIPS',
        'admin_ids',
        'maintenance_notification',
        'avatar_generator',
        'avatar_robohash_set',
        'offense_home',
        'defense_home',
        'moderator_home',
        'verifymail_key',
        'profile_card_disabled_actions',
      ], 'trim'],
      // required fields
      [[
        'teams',
        'require_activation',
        'disable_registration',
        'player_profile',
        'profile_visibility',
        'event_name',
        'event_active',
        'mail_from',
        'mail_fromName',
        'approved_avatar',
        'team_manage_members',
      ], 'required'],
      ['challenge_home', 'default', 'value' => '@web/uploads'],
      ['challenge_root', 'default', 'value' => '/uploads/'],
      [['dn_countryName'], 'default', 'value' => \Yii::$app->sys->dn_countryName],
      [['dn_stateOrProvinceName'], 'default', 'value' => \Yii::$app->sys->dn_stateOrProvinceName],
      [['dn_localityName'], 'default', 'value' => \Yii::$app->sys->dn_localityName],
      [['dn_organizationName'], 'default', 'value' => \Yii::$app->sys->dn_organizationName],
      [['dn_organizationalUnitName'], 'default', 'value' => \Yii::$app->sys->dn_organizationalUnitName],
      ['profile_visibility', 'default', 'value' => 'ingame'],
      [[
        'online_timeout',
        'spins_per_day',
        'members_per_team',
        'target_days_new',
        'target_days_updated',
        'api_claim_timeout',
        'api_target_instances_timeout',
        'api_target_spin_timeout',
        'api_target_spawn_timeout',
        'player_delete_inactive_after',
        'player_delete_deleted_after',
        'player_changed_to_deleted_after',
        'player_delete_rejected_after',
        'pflog_min',
        'pflog_max',
        'stream_record_limit',
        'academic_grouping',
        'failed_login_ip',
        'password_reset_ip',
        'verification_resend_ip',
        'password_reset_email',
        'verification_resend_email',
        'username_length_min',
        'username_length_max',
        'failed_login_ip_timeout',
        'failed_login_username_timeout',
        'password_reset_ip_timeout',
        'password_reset_email_timeout',
        'verification_resend_ip_timeout',
        'verification_resend_email_timeout',
      ], 'integer'],
      [['online_timeout'], 'default', 'value' => 900],
      [['spins_per_day'], 'default', 'value' => 2],
      ['target_days_new', 'default', 'value' => 1],
      ['target_days_updated', 'default', 'value' => 2],
      [['event_start', 'event_end', 'registrations_start', 'registrations_end'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
      [[
        'dashboard_is_home',
        'mail_useFileTransport',
        'mail_verify_peer',
        'mail_verify_peer_name',
        'event_active',
        'teams',
        'team_required',
        'team_manage_members',
        'require_activation',
        'disable_registration',
        'player_profile',
        'approved_avatar',
        'team_manage_members',
        'leaderboard_visible_before_event_start',
        'leaderboard_visible_after_event_end',
        'leaderboard_show_zero',
        'player_monthly_rankings',
        'player_point_rankings',
        'country_rankings',
        'writeup_rankings',
        'disable_mailer',
        'log_failed_claims',
        'team_encrypted_claims_allowed',
        'module_smartcity_disabled',
        'api_bearer_enable',
        'subscriptions_menu_show',
        'subscriptions_emergency_suspend',
        'player_require_approval',
        'player_require_identification',
        'all_players_vip',
        'team_visible_instances',
        'target_hide_inactive',
        'target_guest_view_deny',
        'network_view_guest',
        'hide_timezone',
        'profile_discord',
        'profile_echoctf',
        'profile_twitter',
        'profile_github',
        'profile_htb',
        'profile_twitch',
        'profile_youtube',
        'guest_visible_leaderboards',
        'dashboard_graph_visible',
        'disable_ondemand_operations',
        'maintenance',
        'force_findings_to_claim',
        'disable_mail_validation',
        'module_speedprogramming_enabled',
        'force_https_urls',
        'signup_ValidatemailValidator',
        'signup_StopForumSpamValidator',
        'signup_HourRegistrationValidator',
        'signup_TotalRegistrationsValidator',
        'signup_MXServersValidator',
        'team_only_leaderboards',
      ], 'boolean'],
    ];
  }
  public function attributeLabels()
  {
    return [
      'trust_user_ip' => 'Trust user IP',
      'mac_auth' => 'MAC Authentication',
      'teams' => 'Teams',
      'require_activation' => 'Require activation',
      'disable_registration' => 'Disable registration',
      'player_profile' => 'Player profile',
      'profile_visibility' => 'Player profile visibility',
      'event_name' => 'Event name',
      'site_description' => 'Site Description',
      'offense_home' => 'Offense home',
      'defense_home' => 'Defense home',
      'moderator_home' => 'Moderator home',
      'offense_domain' => 'Offense domain',
      'defense_domain' => 'Defense domain',
      'moderator_domain' => 'Moderator domain',
      'challenge_home' => 'Challenge home',
      'challenge_root' => 'Challenge root',
      'approved_avatar' => 'Approved Avatar',
      'offense_vether_network' => 'Offense vether network',
      'offense_vether_netmask' => 'Offense vether netmask',
      'defense_vether_network' => 'Defense vether network',
      'defense_vether_netmask' => 'Defense vether netmask',
      'offense_registered_tag' => 'Offense registered tag',
      'defense_registered_tag' => 'Defense registered tag',
      'treasure_secret_key' => 'treasure_secret_key',
      'vpngw' => 'VPN Gateway',
      'team_manage_members' => 'Team Manage Members',
      'dashboard_is_home' => 'Dashboard page is home',
      'default_homepage' => 'Default Homepage',
      'mail_from' => 'Mail From',
      'mail_fromName' => 'Mail From Name',
      'mail_host' => 'Mail Host',
      'mail_port' => 'Mail Port',
      'mail_encryption' => 'Mail encryption',
      'mail_verify_peer' => 'Mail verify peer',
      'mail_verify_peer_name' => 'Mail verify peer name',
      'online_timeout' => 'Timeout for user online key to expire',
      'spins_per_day' => 'Spins allowed per day',
      'team_manage_members' => 'Team Manage Members',
      'leaderboard_visible_before_event_start' => 'Leaderboard visible before start',
      'leaderboard_visible_after_event_end' => 'Leaderboard visible after end',
      'leaderboard_show_zero' => 'Leaderboard show zero points',
      'time_zone' => 'Timezone',
      'dn_countryName' => 'countryName',
      'dn_stateOrProvinceName' => 'stateOrProvinceName',
      'dn_localityName' => 'localityName',
      'dn_organizationName' => 'organizationName',
      'dn_organizationalUnitName' => 'organizationalUnitName',
      'target_days_new' => 'Target days is new',
      'target_days_updated' => 'Target days is updated',
      'discord_news_webhook' => 'Discord News Webhook',
      'player_monthly_rankings' => 'Monthly points leaderboards',
      'player_point_rankings' => 'Individual player leaderboards',
      'country_rankings' => 'Country based leaderboards',
      'writeup_rankings' => 'Writeup Ratings',
      'disable_mailer' => 'Disable mailer operations',
      'log_failed_claims' => 'Log failed attempts',
      'team_encrypted_claims_allowed' => 'Team claims',
      'module_smartcity_disabled' => 'Disable SmartCity module',
      'api_bearer_enable' => 'Enable Bearer authorizations API operations',
      'api_claim_timeout' => 'API Claim timeout',
      'api_target_instances_timeout' => 'API Target Instance timeout',
      'api_target_spin_timeout' => 'API Target Spin timeout',
      'api_target_spawn_timeout' => 'API Target spawn timeout',
      'player_delete_inactive_after' => 'Delete innactive players after',
      'player_delete_deleted_after' => 'Delete deleted players after',
      'player_changed_to_deleted_after' => 'Player changed to deleted after',
      'player_delete_rejected_after' => 'Player delete rejected after',
      'mail_verification_token_validity' => 'Mail verification token validity',
      'password_reset_token_validity' => 'Password reset token validity',
      'subscriptions_menu_show' => 'Show subscriptions menu',
      'subscriptions_emergency_suspend' => 'Emergency suspend subscriptions',
      'player_require_approval' => 'Players require approval',
      'player_require_identification' => 'Players require identification',
      'all_players_vip' => 'Give all players VIP status',
      'team_visible_instances' => 'Team instances',
      'target_hide_inactive' => 'Hide inactive targets',
      'target_guest_view_deny' => 'Target deny guest view',
      'network_view_guest' => 'Allow guests network view',
      'hide_timezone' => 'Hide timezone',
      'profile_discord' => 'Discord',
      'profile_echoctf' => 'echoCTF',
      'profile_twitter' => 'Twitter/X',
      'profile_github' => 'Github',
      'profile_htb' => 'HackTheBox',
      'profile_twitch' => 'Twitch',
      'profile_youtube' => 'Youtube',
      'guest_visible_leaderboards' => 'Guest visible leaderboards',
      'dsn' => 'Mail DSN',
      'pflog_min',
      'pflog_max',
    ];
  }

  public function init()
  {
    parent::init();
    foreach ($this->keys as $id) {
      $sysconfig = Sysconfig::findOne($id);
      if ($sysconfig)
        $this->{$id} = $sysconfig->val;
    }
  }

  public function beforeSave() {}

  public function save()
  {
    foreach ($this->keys as $id) {
      $sysconfig = Sysconfig::findOne($id);
      if (!$sysconfig) {
        $sysconfig = new Sysconfig;
        $sysconfig->id = $id;
      }

      if ($this->{$id} !== "" && $this->{$id} !== NULL && $this->{$id} != 0) {
        file_put_contents('/tmp/post.log', "$id=>" . $this->{$id} . "\n", FILE_APPEND);

        $sysconfig->val = $this->{$id};
        $sysconfig->save();
        if ($id === 'time_zone') {
          \Yii::$app->db->createCommand("SET GLOBAL time_zone=(SELECT val FROM sysconfig WHERE id='time_zone')")->execute();
        }
      } else {
        file_put_contents('/tmp/post.log', "delete $id\n", FILE_APPEND);

        $sysconfig->delete();
      }
    }
    Yii::getLogger()->flush(true);

    return true;
  }
}
