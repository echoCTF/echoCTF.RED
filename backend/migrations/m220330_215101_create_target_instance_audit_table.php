<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_instance_audit}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%target}}`
 */
class m220330_215101_create_target_instance_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%target_instance_audit}}', [
            'id' => $this->primaryKey(),
            'op'=>'CHAR(1) NOT NULL DEFAULT "i"',
            'player_id' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'server_id' => $this->integer()->defaultValue(NULL),
            'ip' => $this->integer()->unsigned()->defaultValue(0),
            'reboot' => 'tinyint unsigned not null default 0',
            'ts' => 'TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP',
        ]/* Disable this since its only available on 10.2.5,'ENGINE=RocksDB'*/);
        // 
        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-target_instance_audit-player_id}}',
            '{{%target_instance_audit}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
//        $this->addForeignKey(
//            '{{%fk-target_instance_audit-player_id}}',
//            '{{%target_instance_audit}}',
//            'player_id',
//            '{{%player}}',
//            'id',
//            'CASCADE'
//        );
//
        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-target_instance_audit-target_id}}',
            '{{%target_instance_audit}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
//        $this->addForeignKey(
//            '{{%fk-target_instance_audit-target_id}}',
//            '{{%target_instance_audit}}',
//            'target_id',
//            '{{%target}}',
//            'id',
//            'CASCADE'
//        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-target_instance_audit-player_id}}',
            '{{%target_instance_audit}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-target_instance_audit-target_id}}',
            '{{%target_instance_audit}}'
        );

        $this->dropTable('{{%target_instance_audit}}');
    }
}
