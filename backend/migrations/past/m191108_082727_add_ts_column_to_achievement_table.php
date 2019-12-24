<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%achievement}}`.
 */
class m191108_082727_add_ts_column_to_achievement_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%achievement}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%achievement}}', 'ts');
    }
}
