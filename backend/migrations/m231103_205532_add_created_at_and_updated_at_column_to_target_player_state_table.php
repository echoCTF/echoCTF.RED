<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target_player_state}}`.
 */
class m231103_205532_add_created_at_and_updated_at_column_to_target_player_state_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target_player_state}}', 'created_at', $this->dateTime());
        $this->addColumn('{{%target_player_state}}', 'updated_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target_player_state}}', 'created_at');
        $this->dropColumn('{{%target_player_state}}', 'updated_at');
    }
}
