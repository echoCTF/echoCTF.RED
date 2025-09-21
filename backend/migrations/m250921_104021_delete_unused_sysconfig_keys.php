<?php

use yii\db\Migration;

class m250921_104021_delete_unused_sysconfig_keys extends Migration
{
  public $delkeys = [
    'join_team_with_token',
    'strict_activation',
    'registerForm_academic',
    'registerForm_fullname',
    'patreon_menu',
    'quick_activation',
    'award_points',
  ];

  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    foreach ($this->delkeys as $key)
      $this->delete('sysconfig', ['id' => $key]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}

}
