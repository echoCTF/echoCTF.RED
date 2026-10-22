<?php

use yii\db\Migration;

class m261022_102236_failed_claim_configs extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('sysconfig', ['id' => 'failed_claim_lookup_timeout', 'val' => '1']);
    $this->upsert('sysconfig', ['id' => 'failed_claim_scan_players', 'val' => '1000']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}
}
