<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player}}`.
 */
class m240502_232532_add_approval_column_to_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player}}', 'approval', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player}}', 'approval');
    }
}
