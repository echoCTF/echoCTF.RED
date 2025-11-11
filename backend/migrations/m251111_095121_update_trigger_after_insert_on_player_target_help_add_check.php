<?php

use yii\db\Migration;

class m251111_095121_update_trigger_after_insert_on_player_target_help_add_check extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player_target_help}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_player_target_help}} AFTER INSERT ON {{%player_target_help}} FOR EACH ROW
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
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}