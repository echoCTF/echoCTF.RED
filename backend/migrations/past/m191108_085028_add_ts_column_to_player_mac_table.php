<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%player_mac}}`.
 */
class m191108_085028_add_ts_column_to_player_mac_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_mac}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_mac}}', 'ts');
    }
}
