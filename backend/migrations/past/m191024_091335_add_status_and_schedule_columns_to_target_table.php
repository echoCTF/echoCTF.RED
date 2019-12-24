<?php

use yii\db\Migration;

/**
 * Handles adding status_and_schedule to table `{{%target}}`.
 */
class m191024_091335_add_status_and_schedule_columns_to_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'status', $this->string(32)->defaultValue('offline')->after('active'));
        $this->addColumn('{{%target}}', 'scheduled_at', $this->dateTime()->after('status'));
        $this->db->createCommand("UPDATE {{%target}} SET {{%status}}='online' WHERE {{%active}}=1")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'status');
        $this->dropColumn('{{%target}}', 'scheduled_at');
    }
}
