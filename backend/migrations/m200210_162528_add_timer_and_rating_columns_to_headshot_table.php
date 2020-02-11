<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%headshot}}`.
 */
class m200210_162528_add_timer_and_rating_columns_to_headshot_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%headshot}}', 'timer', $this->bigInteger()->unsigned()->defaultValue(0));
        $this->addColumn('{{%headshot}}', 'rating', $this->smallInteger()->defaultValue(-1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%headshot}}', 'timer');
        $this->dropColumn('{{%headshot}}', 'rating');
    }
}
