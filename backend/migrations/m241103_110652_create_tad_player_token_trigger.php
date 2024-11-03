<?php

use yii\db\Migration;

/**
 * Class m241103_110652_create_tad_player_token_trigger
 */
class m241103_110652_create_tad_player_token_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player_token}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tad_player_token}} AFTER DELETE ON {{%player_token}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  INSERT INTO player_token_history (player_id,`type`,token,description,expires_at,created_at,ts) VALUES (OLD.player_id,OLD.type,OLD.token,OLD.description,OLD.expires_at,OLD.created_at,NOW());
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
