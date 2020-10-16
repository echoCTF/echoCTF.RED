<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%devnull}}`.
 */
class m201016_084541_create_devnull_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%devnull}}', [
            'silence' => 'BLOB',
        ],'ENGINE BLACKHOLE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%devnull}}');
    }
}
