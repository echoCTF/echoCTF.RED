<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%abuser}}`.
 */
class m260827_204704_add_points_and_resolved_column_to_abuser_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('abuser', 'points', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('abuser', 'resolved', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('abuser', 'resolved');
        $this->dropColumn('abuser', 'points');
    }
}
