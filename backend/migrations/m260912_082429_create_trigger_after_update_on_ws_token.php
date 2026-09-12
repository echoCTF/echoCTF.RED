<?php

use yii\db\Migration;

class m260912_082429_create_trigger_after_update_on_ws_token extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_ws_token}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_ws_token}} AFTER UPDATE ON {{%ws_token}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  IF (NEW.token != OLD.token) THEN
    INSERT INTO ws_token_history (`token`,`player_id`,`subject_id`,`is_server`,`expires_at`) VALUES (NEW.token ,NEW.player_id ,NEW.subject_id ,NEW.is_server ,NEW.expires_at);
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