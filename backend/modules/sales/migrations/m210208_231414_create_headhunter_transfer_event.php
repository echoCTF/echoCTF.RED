<?php

use yii\db\Migration;

/**
 * Class m210208_231414_create_headhunter_transfer_event
 */
class m210208_231414_create_headhunter_transfer_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%expire_headhunter_targets}}";
  public $CREATE_SQL="CREATE EVENT {{%expire_headhunter_targets}} ON SCHEDULE EVERY 1 HOUR ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    ALTER EVENT {{%expire_headhunter_targets}} DISABLE;
    DELETE FROM network_target WHERE created_at < NOW() - INTERVAL 1 MONTH;
    ALTER EVENT {{%expire_headhunter_targets}} ENABLE;
  END";
    // Use up()/down() to run migration code without a transaction.
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
