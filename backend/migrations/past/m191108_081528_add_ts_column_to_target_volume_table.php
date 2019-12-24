<?php

use yii\db\Migration;

/**
 * Handles adding created_at_and_updated_at to table `{{%target_volume}}`.
 */
class m191108_081528_add_ts_column_to_target_volume_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target_volume}}','ts', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target_volume}}', 'ts');
    }
}
