<?php

use yii\db\Migration;

/**
 * Class m241103_121633_create_ev_player_token_expiration_event
 */
class m241103_121633_create_ev_player_token_expiration_event extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%ev_player_token_expiration}}";
  public $CREATE_SQL = "CREATE EVENT {{%ev_player_token_expiration}} ON SCHEDULE EVERY 10 SECOND ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    ALTER EVENT {{%ev_player_token_expiration}} DISABLE;
      call expire_player_tokens();
    ALTER EVENT {{%ev_player_token_expiration}} ENABLE;
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
