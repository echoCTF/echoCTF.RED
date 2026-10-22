<?php

use yii\db\Migration;

class m261022_110100_add_vpn_ip_limit_config extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('sysconfig', ['id' => 'vpn_ip_limit', 'val' => '0']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->delete('sysconfig', ['id' => 'vpn_ip_limit']);
  }
}
