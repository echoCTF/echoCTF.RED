<?php

use yii\db\Migration;

/**
 * Class m191007_093100_create_spin_queue_triggers
 */
class m191007_093100_create_spin_queue_triggers extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $SQL="CREATE TRIGGER {{%tad_spin_queue}} AFTER DELETE ON {{%spin_queue}} FOR EACH ROW
BEGIN
  INSERT INTO {{%spin_history}} (target_id,player_id,created_at,updated_at) VALUES (OLD.target_id,OLD.player_id,OLD.created_at,NOW());
END";
  $this->db->createCommand($SQL)->execute();

    }

    public function down()
    {
      $this->db->createCommand("DROP TRIGGER IF EXISTS {{%tad_spin_queue}}")->execute();
      return true;
    }
}
