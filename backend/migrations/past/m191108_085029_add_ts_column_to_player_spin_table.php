<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%player_spin}}`.
 */
class m191108_085029_add_ts_column_to_player_spin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_spin}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_spin}}', 'ts');
    }
}
