<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player}}`.
 */
class m210203_210227_add_stripe_customer_id_column_to_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%player}}', 'stripe_customer_id', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%player}}', 'stripe_customer_id');
    }
}
