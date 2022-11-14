<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target}}`.
 */
class m221114_011301_add_instance_allowed_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'instance_allowed', $this->smallInteger()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'instance_allowed');
    }
}
