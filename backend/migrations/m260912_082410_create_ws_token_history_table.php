<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ws_token_history}}`.
 */
class m260912_082410_create_ws_token_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ws_token_history}}', [
            'id' => $this->primaryKey(),
            'token' => 'varbinary(32) NOT NULL',
            'player_id' => $this->integer()->unsigned(),
            'subject_id' => 'varbinary(32) NOT NULL',
            'is_server' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'expires_at' => $this->dateTime()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ws_token_history}}');
    }
}