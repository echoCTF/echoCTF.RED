<?php

use yii\db\Migration;

/**
 * Class m200210_170928_create_headshot_timer_event
 */
class m200210_170928_create_update_headshot_timer_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%update_headshot_timers}}";
  public $CREATE_SQL="CREATE EVENT {{%update_headshot_timers}} ON SCHEDULE EVERY 10 SECOND ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    DECLARE ltarget_id,lplayer_id INT;
    ALTER EVENT update_headshot_timers DISABLE;
    SELECT target_id,player_id INTO ltarget_id,lplayer_id from headshot where timer=0 order by created_at asc LIMIT 1;
    CALL time_headshot(lplayer_id,ltarget_id);
    ALTER EVENT update_headshot_timers ENABLE;
  END";
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();
    }

    public function down()
    {
    }
}
