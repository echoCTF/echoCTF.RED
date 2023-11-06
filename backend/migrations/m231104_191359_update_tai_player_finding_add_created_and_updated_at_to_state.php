<?php

use yii\db\Migration;

/**
 * Class m231104_191359_update_tai_player_finding_add_created_and_updated_at_to_state
 */
class m231104_191359_update_tai_player_finding_add_created_and_updated_at_to_state extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player_finding}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_player_finding}} AFTER INSERT ON {{%player_finding}} FOR EACH ROW
  thisBegin:BEGIN
  DECLARE local_target_id INT;
  DECLARE headshoted INT default null;
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;

  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('player_finding:',NEW.player_id, ':', NEW.finding_id),NEW.player_id) INTO @devnull;

  CALL add_finding_stream(NEW.player_id,'finding',NEW.finding_id,NEW.points);
  CALL add_player_finding_hint(NEW.player_id,NEW.finding_id);

  SET local_target_id=(SELECT target_id FROM finding WHERE id=NEW.finding_id);
  SET headshoted=(select true as headshoted FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=NEW.player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=NEW.player_id WHERE t.id=local_target_id   GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id));
  IF headshoted IS NOT NULL THEN
    SELECT min(ts),max(ts) INTO min_finding,max_finding FROM player_finding WHERE player_id=NEW.player_id AND finding_id IN (SELECT id FROM finding WHERE target_id=local_target_id);
    SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM player_treasure WHERE player_id=NEW.player_id AND treasure_id IN (SELECT id FROM treasure WHERE target_id=local_target_id);
    SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
    INSERT INTO headshot (player_id,target_id,created_at,timer) VALUES (NEW.player_id,local_target_id,now(),UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'headshot',local_target_id,0,'','','','',now());
  END IF;
  INSERT INTO target_player_state (id,player_id,player_findings,player_points,created_at,updated_at) VALUES (local_target_id,NEW.player_id,1,NEW.points,now(),now()) ON DUPLICATE KEY UPDATE player_findings=player_findings+values(player_findings),player_points=player_points+values(player_points),updated_at=now();
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
