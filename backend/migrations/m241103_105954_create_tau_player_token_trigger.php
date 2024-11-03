<?php

use yii\db\Migration;

/**
 * Class m241103_105954_create_tau_player_token_trigger
 */
class m241103_105954_create_tau_player_token_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_token}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_player_token}} AFTER UPDATE ON {{%player_token}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  IF (NEW.token != OLD.token) THEN
    INSERT INTO player_token_history (player_id,`type`,token,description,expires_at,created_at,ts) VALUES (NEW.player_id,NEW.type,NEW.token,NEW.description,NEW.expires_at,NEW.created_at,NOW());
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
