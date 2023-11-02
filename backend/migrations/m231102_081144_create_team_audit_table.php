<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team_audit}}`.
 */
class m231102_081144_create_team_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%team_audit}}', [
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull(),
            'player_id' => $this->integer(),
            'action' => $this->string(20)->notNull()->defaultValue('default'),
            'message' => $this->text(),
            'ts' => $this->timestamp(),
        ]);
        $this->createIndex('team_id_idx','{{%team_audit}}','team_id');
        $this->createIndex('player_id_idx','{{%team_audit}}','player_id');
        $this->createIndex('action_idx','{{%team_audit}}','action');
        $this->createIndex('ts_idx','{{%team_audit}}','ts');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%team_audit}}');
    }
}
