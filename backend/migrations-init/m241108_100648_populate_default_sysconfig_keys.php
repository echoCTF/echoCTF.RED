<?php

use yii\db\Migration;

/**
 * Class m241108_100648_populate_default_sysconfig_keys
 */
class m241108_100648_populate_default_sysconfig_keys extends Migration
{
  public $keys = [
    'approved_avatar'=>1,
    'avatar_robohash_set'=>'set1',
    'award_points'=>'full',
    'challenge_home'=>'@web/uploads',
    'country_rankings'=>1,
    'dashboard_is_home'=>1,
    'default_homepage'=>'/dashboard',
    'disable_registration'=>0,
    'event_active'=>0,
    'failed_login_ip'=>0,
    'failed_login_ip_timeout'=>900,
    'failed_login_username'=>10,
    'failed_login_username_timeout'=>900,
    'leaderboard_show_zero'=>0,
    'verification_resend_ip' => 0,
    'verification_resend_ip_timeout' => 60 * 60,
    'verification_resend_email' => 3,
    'verification_resend_email_timeout' => 60 * 60,
    'password_reset_ip' => 0,
    'password_reset_ip_timeout' => 60 * 60,
    'password_reset_email' => 3,
    'password_reset_email_timeout' => 60 * 60,
    'signup_TotalRegistrationsValidator' => 0,
    'signup_HourRegistrationValidator' => 0,
    'signup_StopForumSpamValidator' => 0,
    'signup_MXServersValidator' => 0,
    'failed_login_ip' => 0,
    'failed_login_ip_timeout' => 15 * 60,
    'failed_login_username' => 10,
    'failed_login_username_timeout' => 15 * 60,
    'username_length_min' => 4,
    'username_length_max' => 32,
    'player_delete_inactive_after' => 10,
    'player_delete_deleted_after' => 30,
    'player_changed_to_deleted_after' => 10,
    'player_delete_rejected_after' => 5,
    'module_speedprogramming_enabled' => 0,
    'password_reset_token_validity' => '24 hour',
    'mail_verification_token_validity' => '10 day',
    'academic_grouping'=>0,
    'player_monthly_rankings'=>1,
    'online_timeout'=>900,
    'platform_codename'=>'Mycenae',
    'player_point_rankings'=>1,
    'player_profile'=>1,
    'profile_discord'=>1,
    'profile_echoctf'=>1,
    'profile_github'=>1,
    'profile_htb'=>0,
    'profile_twitch'=>1,
    'profile_twitter'=>1,
    'profile_visibility'=>'public',
    'profile_youtube'=>1,
    'require_activation'=>1,
    'spins_per_day'=>10,
    'profile_settings_fields'=>'avatar,bio,country,discord,email,fullname,github,pending_progress,twitch,twitter,username,visibility,youtube',
    'subscriptions_menu_show'=>0,
    'team_required'=>0,
    'teams'=>0,
    'time_zone'=>'UTC',
    'writeup_rankings'=>1,
  ];
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    foreach (Yii::$app->params['dn'] as $key => $val)
      Yii::$app->sys->{'dn_' . $key} = $val;

    foreach ($this->keys as $key => $val)
      $this->upsert('sysconfig', ['id' => $key, 'val' => $val]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    foreach ($this->keys as $key => $val)
      $this->delete('sysconfig', ['id' => $key]);
  }
}
