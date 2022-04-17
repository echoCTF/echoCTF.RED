<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%server}}`.
 */
class m220330_201728_create_server_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%server}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->unique()->notNull(),
            'network' => $this->string(32)->notNull(),
            'ip' => $this->integer()->unsigned()->notNull(),
            'description' => $this->text(),
            'service' => 'ENUM("docker") NOT NULL DEFAULT "docker"',
            'connstr' => $this->string()->notNull(),
            'provider_id' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%server}}');
    }
}
