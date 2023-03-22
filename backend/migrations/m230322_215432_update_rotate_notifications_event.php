<?php

use yii\db\Migration;

/**
 * Class m230322_215432_update_rotate_notifications_event
 */
class m230322_215432_update_rotate_notifications_event extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%rotate_notifications}}";
  public $CREATE_SQL = "CREATE EVENT {{%rotate_notifications}} ON SCHEDULE EVERY 12 HOUR STARTS date_format(now(),'%Y-%m-%d 00:00:01') ON COMPLETION PRESERVE ENABLE DO
    BEGIN
      ALTER EVENT {{%rotate_notifications}} DISABLE;
      DELETE FROM `notification` WHERE
        (`archived`=1 AND date(`updated_at`) < NOW() - INTERVAL 3 HOUR) OR
        (created_at is null and updated_at is null) OR
        (title like '%target%' and date(`created_at`) < now() - interval 3 day);
      ALTER EVENT {{%rotate_notifications}} ENABLE;
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
