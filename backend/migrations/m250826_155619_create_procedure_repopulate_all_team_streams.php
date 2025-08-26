<?php

use yii\db\Migration;

class m250826_155619_create_procedure_repopulate_all_team_streams extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%repopulate_all_team_streams}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%repopulate_all_team_streams}}()
  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE tid INT;
    DECLARE cur CURSOR FOR SELECT id FROM team;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO tid;
      IF done THEN
        LEAVE read_loop;
      END IF;
      CALL repopulate_team_stream(tid);
    END LOOP;
    CLOSE cur;
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