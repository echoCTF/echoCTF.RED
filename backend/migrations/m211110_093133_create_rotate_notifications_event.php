<?php

use yii\db\Migration;

/**
 * Class m211110_093133_create_rotate_notifications_event
 */
class m211110_093133_create_rotate_notifications_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%rotate_notifications}}";
  public $CREATE_SQL="CREATE EVENT {{%rotate_notifications}} ON SCHEDULE EVERY 12 HOUR STARTS date_format(now(),'%Y-%m-%d 00:00:01') ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    ALTER EVENT {{%rotate_notifications}} DISABLE;
    DELETE FROM `notification` WHERE
      (`archived`=1 AND date(`updated_at`) < NOW() - INTERVAL 1 DAY) OR
      (date(`updated_at`) < NOW() - INTERVAL 1 DAY AND title like '%restart%') OR
      (title like '%restart%' AND `archived`=1) OR
      (created_at is null and updated_at is null);
    ALTER EVENT {{%rotate_notifications}} ENABLE;
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
