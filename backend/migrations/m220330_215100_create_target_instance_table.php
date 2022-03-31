<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_instance}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%target}}`
 */
class m220330_215100_create_target_instance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%target_instance}}', [
            'player_id' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'server_id' => $this->integer()->defaultValue(NULL),
            'ip' => $this->integer()->unsigned()->defaultValue(0),
            'reboot' => 'tinyint unsigned not null default 0',
            'created_at' => 'DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'PRIMARY KEY (player_id)',
        ]);

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-target_instance-player_id}}',
            '{{%target_instance}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-target_instance-target_id}}',
            '{{%target_instance}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-target_instance-target_id}}',
            '{{%target_instance}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%target_instance}}');
    }
}
