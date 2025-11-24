<?php

use yii\db\Migration;

class m251117_133332_create_player_payments_history extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_payment_history}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer()->notNull(),
      'payment_id' => $this->string(40)->notNull(),
      'amount' => $this->integer()->unsigned()->defaultValue(0),
      'metadata LONGTEXT COLLATE utf8mb4_bin',
      'created_at' => $this->datetime(),
      'CHECK (JSON_VALID(metadata))',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_payment_history}}');
  }
}
