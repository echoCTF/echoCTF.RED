<?php

use yii\db\Migration;

/**
 * Makes stream_player_target_help control stream logging for ALL writeup
 * activations. When on, a stream row is written with -cost points (0 for
 * free activations) and the existing stream triggers update the scores.
 * When off, nothing is written. Paid activations only happen while
 * stream_player_target_help is on (see WriteupActivation::isPaid()).
 */
class m261026_170000_update_trigger_after_insert_on_player_target_help_stream_toggle extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tai_player_target_help}}";

  public $CREATE_SQL = "CREATE TRIGGER {{%tai_player_target_help}} AFTER INSERT ON {{%player_target_help}} FOR EACH ROW
thisBegin:BEGIN
  DECLARE stream_player_target_help INT;
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  SET stream_player_target_help=memc_get('sysconfig:stream_player_target_help');
  IF stream_player_target_help IS NOT NULL and stream_player_target_help=1 THEN
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'player_target_help',NEW.target_id,-1*NEW.cost,'Activated writeups for {target}','','','',now());
  END IF;
END";

  // Definition from m261026_150100, restored on down()
  public $OLD_SQL = "CREATE TRIGGER {{%tai_player_target_help}} AFTER INSERT ON {{%player_target_help}} FOR EACH ROW
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

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->OLD_SQL)->execute();
  }
}
