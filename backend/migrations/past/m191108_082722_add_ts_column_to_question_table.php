<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%question}}`.
 */
class m191108_082722_add_ts_column_to_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%question}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%question}}', 'ts');
    }
}
