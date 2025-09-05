<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target}}`.
 */
class m250905_085712_add_dynamic_treasures_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'dynamic_treasures', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'dynamic_treasures');
    }
}
