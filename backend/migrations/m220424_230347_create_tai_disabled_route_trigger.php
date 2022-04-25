<?php

use yii\db\Migration;

/**
 * Class m220424_230347_create_tai_disabled_route_trigger
 */
class m220424_230347_create_tai_disabled_route_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_disabled_route}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_disabled_route}} AFTER INSERT ON {{%disabled_route}} FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('route', route) ORDER BY route),']') FROM disabled_route ORDER BY route);
      INSERT INTO sysconfig (id,val) VALUES ('disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
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
