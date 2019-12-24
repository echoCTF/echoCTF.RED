<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%target}}`.
 */
class m191107_161755_add_ts_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'ts', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'ts');
    }
}
