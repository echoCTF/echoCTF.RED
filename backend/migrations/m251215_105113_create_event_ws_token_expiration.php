<?php

use yii\db\Migration;

class m251215_105113_create_event_ws_token_expiration extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%ev_ws_token_expiration}}";
  public $CREATE_SQL = "CREATE EVENT {{%ev_ws_token_expiration}} ON SCHEDULE EVERY 10 MINUTE ON COMPLETION PRESERVE ENABLE DO
  BEGIN
      DELETE FROM ws_token WHERE is_server=0 and expires_at<=NOW();
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