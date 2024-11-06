<?php

use yii\db\Migration;

/**
 * Class m241106_101540_add_player_deletion_sysconfig_keys
 */
class m241106_101540_add_player_deletion_sysconfig_keys extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('sysconfig', ['id' => 'player_delete_inactive_after', 'val' => 10]);
    $this->upsert('sysconfig', ['id' => 'player_delete_deleted_after', 'val' => 30]);
    $this->upsert('sysconfig', ['id' => 'player_changed_to_deleted_after', 'val' => 10]);
    $this->upsert('sysconfig', ['id' => 'player_delete_rejected_after', 'val' => 5]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->upsert('sysconfig', ['id' => 'player_delete_inactive_after']);
    $this->upsert('sysconfig', ['id' => 'player_delete_deleted_after']);
    $this->upsert('sysconfig', ['id' => 'player_changed_to_deleted_after']);
    $this->upsert('sysconfig', ['id' => 'player_delete_rejected_after']);
  }
}
