<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%server}}`.
 */
class m230826_212155_add_ssl_and_timeout_columns_to_server_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%server}}', 'ssl', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%server}}', 'timeout', $this->integer()->notNull()->defaultValue(9000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%server}}', 'ssl');
        $this->dropColumn('{{%server}}', 'timeout');
    }
}
