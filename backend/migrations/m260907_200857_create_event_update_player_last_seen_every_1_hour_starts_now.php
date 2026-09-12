
<?php

use yii\db\Migration;

class m260907_200857_create_event_update_player_last_seen_every_1_hour_starts_now extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%update_player_last_seen}}";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}
