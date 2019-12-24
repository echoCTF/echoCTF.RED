<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%team}}`.
 */
class m191108_085025_add_ts_column_to_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%team}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%team}}', 'ts');
    }
}
