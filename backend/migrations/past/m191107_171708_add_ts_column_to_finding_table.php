<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%finding}}`.
 */
class m191107_171708_add_ts_column_to_finding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%finding}}', 'ts', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%finding}}', 'ts');
    }
}
