<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target}}`.
 */
class m200520_100945_add_timer_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'timer', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'timer');
    }
}
