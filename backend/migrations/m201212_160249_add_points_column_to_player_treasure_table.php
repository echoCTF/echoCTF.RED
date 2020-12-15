<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player_treasure}}`.
 */
class m201212_160249_add_points_column_to_player_treasure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_treasure}}', 'points', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_treasure}}', 'points');
    }
}
