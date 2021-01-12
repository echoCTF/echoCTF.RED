<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%headshot}}`.
 */
class m210112_211036_add_first_column_to_headshot_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%headshot}}', 'first', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%headshot}}', 'first');
    }
}
