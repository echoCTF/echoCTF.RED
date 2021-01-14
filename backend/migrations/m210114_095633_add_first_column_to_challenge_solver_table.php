<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%challenge_solver}}`.
 */
class m210114_095633_add_first_column_to_challenge_solver_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%challenge_solver}}', 'first', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%challenge_solver}}', 'first');
    }
}
