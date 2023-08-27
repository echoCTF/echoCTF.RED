<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target_instance_audit}}`.
 */
class m230826_203504_add_team_allowed_column_to_target_instance_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target_instance_audit}}', 'team_allowed', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target_instance_audit}}', 'team_allowed');
    }
}
