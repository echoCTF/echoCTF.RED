<?php

use yii\db\Migration;

class m260912_082419_create_trigger_after_insert_on_ws_token extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_ws_token}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_ws_token}} AFTER INSERT ON {{%ws_token}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    INSERT INTO ws_token_history (`token`,`player_id`,`subject_id`,`is_server`,`expires_at`) VALUES (NEW.token ,NEW.player_id ,NEW.subject_id ,NEW.is_server ,NEW.expires_at);
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