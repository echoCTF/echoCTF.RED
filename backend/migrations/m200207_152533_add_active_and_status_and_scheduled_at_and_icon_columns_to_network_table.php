<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%network}}`.
 */
class m200207_152533_add_active_and_status_and_scheduled_at_and_icon_columns_to_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%network}}', 'active', $this->boolean()->defaultValue(1));
        $this->addColumn('{{%network}}', 'codename', $this->string());
        $this->addColumn('{{%network}}', 'icon', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%network}}', 'active');
        $this->dropColumn('{{%network}}', 'icon');
    }
}
