<?php

use yii\db\Migration;

/**
 * Class m191015_084720_create_tbu_finding_trigger
 */
class m191015_084720_create_tbu_finding_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbu_finding}}";

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
        echo "m191015_084720_create_tbu_finding_trigger cannot be reverted.\n";

        return false;
    }

    public function up()
    {
      $CREATE_SQL="CREATE TRIGGER {{%tbu_finding}} BEFORE UPDATE ON {{%finding}} FOR EACH ROW
BEGIN
IF NEW.port is NULL THEN
  SET NEW.port=0;
END IF;
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
