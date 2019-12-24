<?php

use yii\db\Migration;

/**
 * Handles adding created_at_and_updated_at to table `{{%credential}}`.
 */
class m191108_081524_add_ts_column_to_credential_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%credential}}', 'ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%credential}}', 'ts');
    }
}
