<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%profile}}`.
 */
class m211228_092925_add_pending_progress_column_to_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'pending_progress', $this->boolean()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'pending_progress');
    }
}
