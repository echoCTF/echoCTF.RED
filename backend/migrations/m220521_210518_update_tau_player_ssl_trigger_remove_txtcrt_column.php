<?php

use yii\db\Migration;

/**
 * Class m220521_210518_update_tau_player_ssl_trigger_remove_txtcrt_column
 */
class m220521_210518_update_tau_player_ssl_trigger_remove_txtcrt_column extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_ssl}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_player_ssl}} AFTER UPDATE ON {{%player_ssl}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF OLD.subject!=NEW.subject OR OLD.csr!=NEW.csr OR OLD.crt!=NEW.crt OR OLD.privkey!=NEW.privkey and OLD.subject is not null and OLD.subject!='' THEN
      INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.privkey,NOW());
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
