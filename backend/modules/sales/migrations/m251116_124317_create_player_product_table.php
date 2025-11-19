<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_product}}`.
 */
class m251116_124317_create_player_product_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_product}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer()->notNull(),
      'product_id' => $this->string(40)->notNull(),
      'price_id' => $this->string(32)->notNull(),
      'ending'=>$this->datetime(),
      'metadata LONGTEXT COLLATE utf8mb4_bin',
      'created_at'=>$this->datetime(),
      'updated_at'=>$this->datetime(),
      'CHECK (JSON_VALID(metadata))'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_product}}');
  }
}
