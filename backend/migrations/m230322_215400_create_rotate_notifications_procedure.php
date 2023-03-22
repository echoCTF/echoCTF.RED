<?php

use yii\db\Migration;

/**
 * Class m230322_220536_create_rotate_notifications_procedure
 */
class m230322_215400_create_rotate_notifications_procedure extends Migration
{
    public $DROP_SQL = "DROP PROCEDURE IF EXISTS {{%rotate_notifications}}";
    public $CREATE_SQL = "CREATE PROCEDURE {{%rotate_notifications}} (IN archived_interval_minute INT, IN pending_interval_minute INT)
    BEGIN
      DELETE FROM `notification` WHERE
        (`archived`=1 AND DATE(`updated_at`) < NOW() - INTERVAL archived_interval_minute MINUTE) OR
        (created_at IS null AND updated_at IS null) OR
        (title LIKE '%target%' and DATE(created_at) < NOW() - INTERVAL pending_interval_minute MINUTE);
    END";
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($this->CREATE_SQL)->execute();
    }

    public function down()
    {
        $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
