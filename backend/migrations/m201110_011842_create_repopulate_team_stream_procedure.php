<?php

use yii\db\Migration;

/**
 * Class m201110_011842_create_repopulate_team_stream_procedure
 */
class m201110_011842_create_repopulate_team_stream_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%repopulate_team_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%repopulate_team_stream}} (IN tid INT)
  BEGIN
    DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
    IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
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
