<?php

use yii\db\Migration;

/**
 * Handles adding suggested_xp to table `{{%target}}`.
 */
class m191107_134008_add_suggested_xp_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'suggested_xp', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'suggested_xp');
    }
}
