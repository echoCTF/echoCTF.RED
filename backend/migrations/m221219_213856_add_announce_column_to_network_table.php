<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%network}}`.
 */
class m221219_213856_add_announce_column_to_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%network}}', 'announce', $this->boolean()->defaultValue(1)->after('active'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%network}}', 'announce');
    }
}
