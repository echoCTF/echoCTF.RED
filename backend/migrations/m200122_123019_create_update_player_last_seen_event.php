<?php

use yii\db\Migration;

/**
 * Class m200122_123019_create_update_player_last_seen_event
 */
class m200122_123019_create_update_player_last_seen_event extends Migration
{
    public $DROP_SQL="DROP EVENT IF EXISTS {{%update_player_last_seen}}";
    public $CREATE_SQL="CREATE EVENT {{%update_player_last_seen}} ON SCHEDULE EVERY 1 HOUR ON COMPLETION PRESERVE ENABLE DO BEGIN
     UPDATE `player_last` SET `on_pui`=memc_get(CONCAT('last_seen:',id)) WHERE memc_get(CONCAT('last_seen:',id)) IS NOT NULL;
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
