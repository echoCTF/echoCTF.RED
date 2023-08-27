<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%target_instance}}`.
 */
class m230826_200413_add_team_allowed_column_to_target_instance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target_instance}}', 'team_allowed', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target_instance}}', 'team_allowed');
    }
}
