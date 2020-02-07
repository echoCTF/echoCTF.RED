<?php

use yii\db\Migration;

/**
 * Class m200203_082812_add_schedule_spin_after_headshot_on_triggers
 */
class m200203_082812_add_schedule_spin_after_headshot_on_triggers extends Migration
{
    public $QUERIES=[
      [
        'drop'=>"DROP TRIGGER IF EXISTS {{%tai_headshot}}",
        'create'=>"CREATE TRIGGER {{%tai_headshot}} AFTER INSERT ON {{%headshot}} FOR EACH ROW
        thisBegin:BEGIN
          IF (@TRIGGER_CHECKS = FALSE) THEN
            LEAVE thisBegin;
          END IF;
          INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
        END"
      ]
    ];
    public function up()
    {
      foreach($this->QUERIES as $task)
      {
        $this->db->createCommand($task['drop'])->execute();
        $this->db->createCommand($task['create'])->execute();
      }
    }

    public function down()
    {
      foreach($this->QUERIES as $task)
      {
        $this->db->createCommand($task['drop'])->execute();
      }
    }

}
