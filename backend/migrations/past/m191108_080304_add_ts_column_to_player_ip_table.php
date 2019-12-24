<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%player_ip}}`.
 */
class m191108_080304_add_ts_column_to_player_ip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_ip}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_ip}}', 'ts');
    }
}
