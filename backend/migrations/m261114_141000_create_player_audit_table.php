<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_audit}}`.
 * Changes to player details, filled by the tau_player trigger.
 * No foreign key so the entries stay after the player is deleted.
 */
class m261114_141000_create_player_audit_table extends Migration
{
  public function safeUp()
  {
    $this->createTable('{{%player_audit}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer(10)->unsigned()->notNull(),
      'field' => $this->string(32)->notNull(),
      'old_value' => $this->string(255),
      'new_value' => $this->string(255),
      'ts' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
    ]);

    $this->createIndex('idx-player_audit-player_id', '{{%player_audit}}', 'player_id');
    $this->createIndex('idx-player_audit-field', '{{%player_audit}}', 'field');
    $this->createIndex('idx-player_audit-ts', '{{%player_audit}}', 'ts');
  }

  public function safeDown()
  {
    $this->dropTable('{{%player_audit}}');
  }
}
