<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%challenge}}`.
 */
class m191108_082724_add_ts_column_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%challenge}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%challenge}}', 'ts');
    }
}
