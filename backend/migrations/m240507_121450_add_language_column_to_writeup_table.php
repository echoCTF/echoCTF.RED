<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%writeup}}`.
 */
class m240507_121450_add_language_column_to_writeup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%writeup}}', 'language_id', $this->string(8)->after('formatter')->notNull()->defaultValue('en'));
        $this->addForeignKey('fk_language_id', 'writeup', 'language_id', 'language', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%writeup}}', 'language');
    }
}
