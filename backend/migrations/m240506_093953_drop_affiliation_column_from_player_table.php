<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%player}}`.
 */
class m240506_093953_drop_affiliation_column_from_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%player}}', 'affiliation');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%player}}', 'affiliation', $this->integer());
    }
}
