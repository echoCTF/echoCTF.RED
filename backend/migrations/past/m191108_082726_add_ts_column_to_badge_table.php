<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%badge}}`.
 */
class m191108_082726_add_ts_column_to_badge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%badge}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%badge}}', 'ts');
    }
}
