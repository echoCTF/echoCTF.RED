<?php

use yii\db\Migration;

/**
 * Class m191105_103615_create_tau_player_ssl_trigger
 */
class m191105_103615_create_tau_player_ssl_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_ssl}}";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

      $CREATE_SQL="CREATE TRIGGER {{%tau_player_ssl}} AFTER UPDATE ON {{%player_ssl}} FOR EACH ROW
      BEGIN
        IF OLD.subject!=NEW.subject OR OLD.csr!=NEW.csr OR OLD.crt!=NEW.crt OR OLD.privkey!=NEW.privkey THEN
          INSERT INTO {{%crl}} values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.txtcrt,OLD.privkey,NOW());
        END IF;
      END";
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
