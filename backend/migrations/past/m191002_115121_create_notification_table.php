<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m191002_115121_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("DROP TABLE IF EXISTS {{%notification}}")->execute();

        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(),
            'body' => $this->text(),
            'archived' => $this->boolean()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-notification-player_id}}',
            '{{%notification}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-notification-player_id}}',
            '{{%notification}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-notification-player_id}}',
            '{{%notification}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-notification-player_id}}',
            '{{%notification}}'
        );

        $this->dropTable('{{%notification}}');
    }
}
