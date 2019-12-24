<?php

use yii\db\Migration;

/**
 * Handles adding created_at_and_updated_at to table `{{%target_variable}}`.
 */
class m191108_081529_add_ts_column_to_target_variable_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target_variable}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target_variable}}', 'ts');
    }
}
