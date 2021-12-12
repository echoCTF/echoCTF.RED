<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%banned_mx_servers}}`.
 */
class m211212_093135_create_banned_mx_server_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%banned_mx_server}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique(),
            'notes' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at'=>$this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%banned_mx_server}}');
    }
}
