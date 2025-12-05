<?php

use yii\db\Migration;

class m251118_110104_create_player_product_expirations_event extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%ev_player_product_expiration}}";
  public $CREATE_SQL = "CREATE EVENT {{%ev_player_product_expiration}} ON SCHEDULE EVERY 1 HOUR ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    ALTER EVENT {{%ev_player_product_expiration}} DISABLE;
      call expire_player_products();
    ALTER EVENT {{%ev_player_product_expiration}} ENABLE;
  END";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    //$this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}
