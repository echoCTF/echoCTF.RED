<?php

use yii\db\Migration;

/**
 * Class m191105_105456_create_update_tad_player_trigger
 */
class m191105_105456_create_tbd_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbd_player}}";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

      $CREATE_SQL="CREATE TRIGGER {{%tbd_player}} BEFORE DELETE ON {{%player}} FOR EACH ROW
      BEGIN
        DELETE FROM player_ssl WHERE player_id=OLD.id;
      END";
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
