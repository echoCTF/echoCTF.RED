<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%banned_player}}`.
 */
class m191030_065354_create_banned_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%banned_player}}', [
            'id' => $this->primaryKey(),
            'old_id' => $this->integer(),
            'username' => $this->string(32),
            'email' => $this->string(128)->unique(),
            'registered_at'=> $this->dateTime(),
            'banned_at'=> $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%banned_player}}');
    }
}
