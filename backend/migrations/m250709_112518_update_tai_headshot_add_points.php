<?php

use yii\db\Migration;

class m250709_112518_update_tai_headshot_add_points extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_headshot}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_headshot}} AFTER INSERT ON {{%headshot}} FOR EACH ROW
  thisBegin:BEGIN
  DECLARE private_instance int;
  DECLARE local_points int;
  DECLARE lheadshot_points int;
  DECLARE lfirst_headshot_points int;
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
   SET local_points=0;
   SELECT headshot_points,first_headshot_points INTO lheadshot_points,lfirst_headshot_points FROM target WHERE id=NEW.target_id;
   SET private_instance=(SELECT COUNT(*) FROM target_instance WHERE player_id=NEW.player_id AND target_id=NEW.target_id);
   IF (SELECT headshot_spin FROM target WHERE id=NEW.target_id)>0 AND private_instance<1 THEN
     INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
   ELSEIF private_instance>0 THEN
     UPDATE target_instance SET reboot=2 WHERE player_id=NEW.player_id AND target_id=NEW.target_id;
   END IF;
   IF (SELECT count(*) FROM target_ondemand WHERE target_id=NEW.target_id AND state=1)>0 THEN
       UPDATE target_ondemand SET heartbeat=(NOW() - INTERVAL 59 MINUTE - INTERVAL 30 SECOND) WHERE target_id=NEW.target_id;
   END IF;
   IF NEW.first = 1 AND lfirst_headshot_points>0 THEN
     SET local_points=lfirst_headshot_points;
   ELSEIF lheadshot_points>0 THEN
     SET local_points=lheadshot_points;
   END IF;
   INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'headshot',NEW.target_id,local_points,'','','','',now());
   UPDATE target_state SET total_headshots=total_headshots+1,timer_avg=(SELECT ifnull(round(avg(timer)),0) FROM headshot WHERE target_id=NEW.target_id) WHERE id=NEW.target_id;
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