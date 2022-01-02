<?php

use yii\db\Migration;

/**
 * Class m211230_134128_update_tai_url_route_trigger
 */
class m211230_134128_update_tai_url_route_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_url_route}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_url_route}} AFTER INSERT ON {{%url_route}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE routes LONGTEXT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('source', source, 'destination', destination) ORDER BY weight,source,destination),']') FROM url_route ORDER BY weight, source, destination);
    INSERT INTO sysconfig (id,val) VALUES ('routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
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
