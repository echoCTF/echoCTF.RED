<?php

use yii\db\Migration;

/**
 * Handles adding created_at to table `{{%treasure}}`.
 */
class m191108_080443_add_ts_column_to_treasure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%treasure}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%treasure}}', 'ts');
    }
}
