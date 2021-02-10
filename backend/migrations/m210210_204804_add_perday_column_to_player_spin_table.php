<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player_spin}}`.
 */
class m210210_204804_add_perday_column_to_player_spin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%player_spin}}', 'perday', $this->tinyInteger());
        $this->db->createCommand("UPDATE player_spin SET perday=(select val from sysconfig where id='spins_per_day')")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%player_spin}}', 'perday');
    }
}
