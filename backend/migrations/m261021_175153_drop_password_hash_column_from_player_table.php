<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%player}}`.
 */
class m261021_175153_drop_password_hash_column_from_player_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->dropColumn('player', 'password_hash');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}
}
