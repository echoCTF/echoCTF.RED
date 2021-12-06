<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target}}`.
 */
class m211206_200949_add_writeup_allowed_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'writeup_allowed', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'writeup_allowed');
    }
}
