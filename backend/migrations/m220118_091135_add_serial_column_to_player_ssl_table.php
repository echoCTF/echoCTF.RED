<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player_ssl}}`.
 */
class m220118_091135_add_serial_column_to_player_ssl_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_ssl}}', 'serial', 'BIGINT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT AFTER player_id');
        $this->db->createCommand("ALTER TABLE {{%player_ssl}} AUTO_INCREMENT=1600000000")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_ssl}}', 'serial');
    }
}
