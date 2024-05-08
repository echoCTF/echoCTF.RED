<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%team}}`.
 */
class m240507_234037_add_locked_column_to_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%team}}', 'locked', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%team}}', 'locked');
    }
}
