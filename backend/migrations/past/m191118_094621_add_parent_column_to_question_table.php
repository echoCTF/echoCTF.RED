<?php

use yii\db\Migration;

/**
 * Handles adding parent to table `{{%question}}`.
 */
class m191118_094621_add_parent_column_to_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%question}}', 'parent', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%question}}', 'parent');
    }
}
