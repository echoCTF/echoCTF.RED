<?php

use yii\db\Migration;

class m250826_170248_update_procedure_repopulate_team_stream extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%repopulate_team_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%repopulate_team_stream}}(IN tid INT)
  BEGIN
    DECLARE `_rollback` BOOL DEFAULT false;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = true;
    IF (SELECT count(*) FROM sysconfig WHERE id='teams' AND val=1)>0 AND (SELECT count(*) FROM team WHERE id=tid)>0 THEN
      START TRANSACTION;
      UPDATE team_score SET points=0 WHERE team_id=tid;
      DELETE FROM team_stream WHERE team_id=tid;
      INSERT INTO team_stream SELECT tid,model,model_id,points,ts FROM stream WHERE model!='user' AND player_id IN (select player_id FROM team_player WHERE team_id=tid) GROUP BY model,model_id ORDER BY id,ts;
      IF `_rollback` THEN
          ROLLBACK;
      ELSE
          COMMIT;
      END IF;
    END IF;
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