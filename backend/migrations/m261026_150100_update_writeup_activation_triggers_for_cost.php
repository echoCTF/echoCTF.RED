<?php

use yii\db\Migration;

/**
 * Makes the writeup activation triggers aware of player_target_help.cost.
 *
 * * tbi_player_treasure / tbi_player_finding: halve points only for legacy
 *   activations (cost=0). Paid activations claim full points.
 * * tai_player_target_help: a paid activation always writes a negative
 *   stream row so player_score, team_stream and team_score pick up the
 *   charge. Free activations keep honoring stream_player_target_help.
 */
class m261026_150100_update_writeup_activation_triggers_for_cost extends Migration
{
  public $TREASURE_SQL = "CREATE TRIGGER {{%tbi_player_treasure}} BEFORE INSERT ON {{%player_treasure}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM treasure WHERE id=NEW.treasure_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id AND cost=0)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END";

  public $FINDING_SQL = "CREATE TRIGGER {{%tbi_player_finding}} BEFORE INSERT ON {{%player_finding}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM finding WHERE id=NEW.finding_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id AND cost=0)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END";

  public $PTH_SQL = "CREATE TRIGGER {{%tai_player_target_help}} AFTER INSERT ON {{%player_target_help}} FOR EACH ROW
thisBegin:BEGIN
  DECLARE stream_player_target_help INT;
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  IF NEW.cost > 0 THEN
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'player_target_help',NEW.target_id,-1*NEW.cost,'Activated writeups for {target}','','','',now());
    LEAVE thisBegin;
  END IF;
  SET stream_player_target_help=memc_get('sysconfig:stream_player_target_help');
  IF stream_player_target_help IS NOT NULL and stream_player_target_help=1 THEN
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'player_target_help',NEW.target_id,0,'Activated writeups for {target}','','','',now());
  END IF;
END";

  // Previous definitions, restored on down()
  public $OLD_TREASURE_SQL = "CREATE TRIGGER {{%tbi_player_treasure}} BEFORE INSERT ON {{%player_treasure}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM treasure WHERE id=NEW.treasure_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END";

  public $OLD_FINDING_SQL = "CREATE TRIGGER {{%tbi_player_finding}} BEFORE INSERT ON {{%player_finding}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM finding WHERE id=NEW.finding_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END";

  public $OLD_PTH_SQL = "CREATE TRIGGER {{%tai_player_target_help}} AFTER INSERT ON {{%player_target_help}} FOR EACH ROW
thisBegin:BEGIN
  DECLARE stream_player_target_help INT;
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  SET stream_player_target_help=memc_get('sysconfig:stream_player_target_help');
  IF stream_player_target_help IS NOT NULL and stream_player_target_help=1 THEN
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'player_target_help',NEW.target_id,0,'Activated writeups for {target}','','','',now());
  END IF;
END";

  public function up()
  {
    $this->recreate('tbi_player_treasure', $this->TREASURE_SQL);
    $this->recreate('tbi_player_finding', $this->FINDING_SQL);
    $this->recreate('tai_player_target_help', $this->PTH_SQL);
  }

  public function down()
  {
    $this->recreate('tbi_player_treasure', $this->OLD_TREASURE_SQL);
    $this->recreate('tbi_player_finding', $this->OLD_FINDING_SQL);
    $this->recreate('tai_player_target_help', $this->OLD_PTH_SQL);
  }

  private function recreate(string $name, string $sql)
  {
    $this->db->createCommand("DROP TRIGGER IF EXISTS {{%$name}}")->execute();
    $this->db->createCommand($sql)->execute();
  }
}
