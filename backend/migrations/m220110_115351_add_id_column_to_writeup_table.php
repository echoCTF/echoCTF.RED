<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%writeup}}`.
 */
class m220110_115351_add_id_column_to_writeup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%writeup}}', 'id', 'INTEGER NOT NULL UNIQUE AUTO_INCREMENT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%writeup}}', 'id');
    }
}
