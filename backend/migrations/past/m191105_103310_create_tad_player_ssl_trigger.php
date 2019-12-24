<?php

use yii\db\Migration;

/**
 * Class m191105_103310_create_tad_player_ssl_trigger
 */
class m191105_103310_create_tad_player_ssl_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player_ssl}}";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

      $CREATE_SQL="CREATE TRIGGER {{%tad_player_ssl}} AFTER DELETE ON {{%player_ssl}} FOR EACH ROW
      BEGIN
        INSERT INTO {{%crl}} values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.txtcrt,OLD.privkey,NOW());
      END";
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
