<?php

use yii\db\Migration;

/**
 * Class m220120_225403_switch_encoding
 */
class m220120_225403_switch_database_and_table_encodings extends Migration
{
    public $tables=[
      'achievement',
      'avatar',
      'badge',
      'badge_finding',
      'badge_treasure',
      'banned_mx_server',
      'banned_player',
      'challenge',
      'challenge_solver',
      'country',
      'credential',
      'crl',
      'debuglogs',
      'devnull',
      'disabled_route',
      'email_template',
      'experience',
      'faq',
      'finding',
      'headshot',
      'hint',
      'infrastructure',
      'infrastructure_target',
      'inquiry',
      'instruction',
      'level',
      'migration',
      'migration_red',
      'muisess',
      'network',
      'network_player',
      'network_target',
      'news',
      'notification',
      'objective',
      'player',
      'player_badge',
      'player_counter_nf',
      'player_country_rank',
      'player_disabledroute',
      'player_finding',
      'player_hint',
      'player_last',
      'player_question',
      'player_rank',
      'player_relation',
      'player_score',
      'player_spin',
      'player_ssl',
//      'player_subscription',
      'player_target_help',
      'player_treasure',
      'player_tutorial_task',
      'player_vpn_history',
//      'product',
//      'product_network',
      'profile',
      'question',
      'report',
      'rule',
      'sessions',
      'spin_history',
      'spin_queue',
      'stream',
//      'stripe_webhook',
      'sysconfig',
      'target',
      'target_metadata',
      'target_ondemand',
      'target_variable',
      'target_volume',
      'team',
      'team_player',
      'team_rank',
      'team_score',
      'team_stream',
      'treasure',
      'treasure_action',
      'tutorial',
      'tutorial_target',
      'tutorial_task',
      'tutorial_task_dependency',
      'url_route',
      'user',
      'vpn_template',
      'writeup',
      'writeup_rating',
    ];
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $this->execute("SET foreign_key_checks = 0");
      foreach($this->tables as $table)
        $this->execute("ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
      $this->execute("SET foreign_key_checks = 1");
      $db=$this->db->createCommand("SELECT DATABASE() FROM DUAL")->queryScalar();
      $this->execute("alter database $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }

    public function down()
    {
        echo "m220120_225403_switch_encoding cannot be reverted.\n";

    }
}
