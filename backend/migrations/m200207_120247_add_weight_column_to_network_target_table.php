<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%network_target}}`.
 */
class m200207_120247_add_weight_column_to_network_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%network_target}}', 'weight', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%network_target}}', 'weight');
    }
}
