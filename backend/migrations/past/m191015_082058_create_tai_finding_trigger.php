<?php

use yii\db\Migration;

/**
 * Class m191015_082058_create_tai_finding_trigger
 */
class m191015_082058_create_tai_finding_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_finding}}";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191015_082058_create_tai_finding_trigger cannot be reverted.\n";

        return false;
    }

    public function up()
    {
    $CREATE_SQL="CREATE TRIGGER {{%tai_finding}} AFTER INSERT ON {{%finding}} FOR EACH ROW
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id) INTO @devnull;
END";

      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($CREATE_SQL)->execute();

    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      return true;
    }
}
