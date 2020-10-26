<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%challenge}}`.
 */
class m201026_115007_add_active_column_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%challenge}}', 'active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%challenge}}', 'active');
    }
}
