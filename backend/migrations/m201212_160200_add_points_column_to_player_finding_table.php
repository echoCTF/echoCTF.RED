<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player_finding}}`.
 */
class m201212_160200_add_points_column_to_player_finding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_finding}}', 'points', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_finding}}', 'points');
    }
}
