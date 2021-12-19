<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_counter_nf}}`.
 */
class m211218_221244_create_player_counter_nf_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_counter_nf}}', [
            'player_id' => $this->primaryKey(),
            'metric' => $this->string()->notNull(),
            'counter' => $this->bigInteger()->notNull()->defaultValue(0),
            'UNIQUE KEY (player_id,metric)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player_counter_nf}}');
    }
}
