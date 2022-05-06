<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_score_monthly}}`.
 */
class m220506_092818_create_player_score_monthly_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_score_monthly}}', [
            'player_id' => $this->integer()->notNull(),
            'points' => $this->bigInteger()->notNull()->defaultValue(0),
            'dated_at' => $this->integer()->notNull(),
            'ts' => $this->timestamp(),
            'PRIMARY KEY (player_id,dated_at)'
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player_score_monthly}}');
    }
}
