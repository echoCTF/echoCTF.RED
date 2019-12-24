<?php

use yii\db\Migration;

/**
 * Class m191107_133122_alter_column_vpn_local_address_on_table_player_vpn_history
 */
class m191107_133122_alter_column_vpn_local_address_on_table_player_vpn_history extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->alterColumn('{{%player_last}}','vpn_local_address',$this->integer()->unsigned());
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->alterColumn('{{%player_last}}','vpn_local_address',$this->integer());
  }
}
