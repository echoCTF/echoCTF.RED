<?php

use yii\db\Migration;

/**
 * Class m220531_111646_update_tai_target_add_target_state
 */
class m220531_111646_update_tai_target_add_target_state extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_target}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_target}} AFTER INSERT ON {{%target}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF (select memc_server_count()<1) THEN
        DO memc_servers_set('127.0.0.1');
    END IF;
    INSERT IGNORE INTO target_state (id) values (NEW.id);
    DO memc_set(CONCAT('target:',NEW.id),NEW.ip);
    DO memc_set(CONCAT('target:',NEW.ip),NEW.id);
    END";

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
