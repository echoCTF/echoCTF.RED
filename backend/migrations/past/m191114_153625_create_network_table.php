<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%network}}`.
 */
class m191114_153625_create_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%network}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'description' => $this->text(),
            'public' => $this->boolean()->defaultValue(1),
            'ts' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%network}}');
    }
}
