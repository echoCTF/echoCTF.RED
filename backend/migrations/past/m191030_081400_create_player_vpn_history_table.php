<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_vpn_history}}`.
 */
class m191030_081400_create_player_vpn_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_vpn_history}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer(),
            'vpn_remote_address' => $this->integer(),
            'vpn_local_address' => $this->integer(),
            'ts'=>$this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player_vpn_history}}');
    }
}
