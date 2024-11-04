<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%player}}`.
 */
class m241104_201004_drop_token_columns_from_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropColumn('player', 'password_reset_token');
      $this->dropColumn('player', 'verification_token');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->addColumn('player', 'password_reset_token', $this->string());
      $this->addColumn('player', 'verification_token', $this->string());
    }
}
