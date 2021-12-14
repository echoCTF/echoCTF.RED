<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target}}`.
 */
class m211214_203948_add_player_spin_and_headshot_spin_columns_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'player_spin', $this->boolean()->defaultValue(1));
        $this->addColumn('{{%target}}', 'headshot_spin', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'player_spin');
        $this->dropColumn('{{%target}}', 'headshot_spin');
    }
}
