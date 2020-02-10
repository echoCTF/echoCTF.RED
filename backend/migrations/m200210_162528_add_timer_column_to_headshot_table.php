<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%headshot}}`.
 */
class m200210_162528_add_timer_column_to_headshot_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%headshot}}', 'timer', $this->bigInteger()->unsigned()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%headshot}}', 'timer');
    }
}
