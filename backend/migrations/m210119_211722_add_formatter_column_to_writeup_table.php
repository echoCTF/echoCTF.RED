<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%writeup}}`.
 */
class m210119_211722_add_formatter_column_to_writeup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%writeup}}', 'formatter', $this->string()->notNull()->defaultValue('text'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%writeup}}', 'formatter');
    }
}
