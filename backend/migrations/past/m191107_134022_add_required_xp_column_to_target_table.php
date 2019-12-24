<?php

use yii\db\Migration;

/**
 * Handles adding required_xp to table `{{%target}}`.
 */
class m191107_134022_add_required_xp_column_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'required_xp', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'required_xp');
    }
}
