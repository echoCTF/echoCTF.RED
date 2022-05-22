<?php

use yii\db\Migration;

/**
 * Class m220522_232612_update_tad_player_ssl_trigger_remove_txtcrt
 */
class m220522_232612_update_tad_player_ssl_trigger_remove_txtcrt extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player_ssl}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_player_ssl}} AFTER DELETE ON {{%player_ssl}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.privkey,NOW());
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
