<?php

use yii\db\Migration;

class m261012_084135_add_server_fk_to_target_instance extends Migration
{
  public function safeUp()
  {
    $this->createIndex(
      'idx-target_instance-server_id',
      '{{%target_instance}}',
      'server_id'
    );

    $this->addForeignKey(
      'fk-target_instance-server_id',
      '{{%target_instance}}',
      'server_id',
      '{{%server}}',
      'id',
      'SET NULL',
      'CASCADE'
    );
  }

  public function safeDown()
  {
    $this->dropForeignKey('fk-target_instance-server_id', '{{%target_instance}}');
    $this->dropIndex('idx-target_instance-server_id', '{{%target_instance}}');
  }
}
