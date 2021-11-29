<?php

use yii\db\Migration;

/**
 * Class m211129_224218_update_init_mysql_procedure_set_timezone
 */
class m211129_224218_update_init_mysql_procedure_set_timezone extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%init_mysql}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%init_mysql}} ()
  BEGIN
    IF (SELECT val FROM sysconfig WHERE id='time_zone') IS NOT NULL THEN
      SET GLOBAL time_zone=(SELECT val FROM sysconfig WHERE id='time_zone');
    END IF;
    call populate_memcache();
    call calculate_ranks();
    call calculate_country_rank();
    call calculate_team_ranks();
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
