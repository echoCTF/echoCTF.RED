<?php

use yii\db\Migration;

class m261021_180041_make_codes_and_tokens_case_sensitive extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->alterColumn('treasure', 'code', $this->string(128)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_bin'));
    $this->alterColumn('question', 'code', $this->string(128)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_bin'));
    //$this->alterColumn('player_token', 'token', $this->string(128)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL'));
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}
}
