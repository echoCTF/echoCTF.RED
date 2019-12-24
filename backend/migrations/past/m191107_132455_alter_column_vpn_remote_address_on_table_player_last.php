<?php

use yii\db\Migration;

/**
 * Class m191107_132455_alter_column_vpn_remote_address_on_table_player_last
 */
class m191107_132455_alter_column_vpn_remote_address_on_table_player_last extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->alterColumn('{{%player_last}}','vpn_remote_address',$this->integer()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->alterColumn('{{%player_last}}','vpn_remote_address',$this->integer());
    }
}
