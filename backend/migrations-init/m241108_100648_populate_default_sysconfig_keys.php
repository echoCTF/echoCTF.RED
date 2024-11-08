<?php

use yii\db\Migration;

/**
 * Class m241108_100648_populate_default_sysconfig_keys
 */
class m241108_100648_populate_default_sysconfig_keys extends Migration
{
  public $keys = [
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
    'module_speedprogramming_disabled' => 1,
    'password_reset_token_validity' => '24 hour',
    'mail_verification_token_validity' => '10 day',
    'academic_grouping'=>0,
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
