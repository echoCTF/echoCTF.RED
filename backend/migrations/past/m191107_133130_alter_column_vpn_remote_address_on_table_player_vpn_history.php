<?php

use yii\db\Migration;

/**
 * Class m191107_133130_alter_column_vpn_remote_address_on_table_player_vpn_history
 */
class m191107_133130_alter_column_vpn_remote_address_on_table_player_vpn_history extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->alterColumn('{{%player_vpn_history}}','vpn_remote_address',$this->integer()->unsigned());
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->alterColumn('{{%player_vpn_history}}','vpn_remote_address',$this->integer());
  }
}
