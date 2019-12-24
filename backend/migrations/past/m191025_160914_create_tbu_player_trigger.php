<?php

use yii\db\Migration;

/**
 * Class m191025_160914_create_tbu_player_trigger
 */
class m191025_160914_create_tbu_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbu_player}}";

    public function up()
    {
      $CREATE_SQL="CREATE TRIGGER {{%tbu_player}} BEFORE UPDATE ON {{%player}} FOR EACH ROW
BEGIN
  SET NEW.ts=UTC_TIMESTAMP();
  IF NEW.status IS NOT NULL  AND NEW.status!=OLD.status AND NEW.status>0 THEN
    SET NEW.active=0;
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
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }
    DROP TRIGGER IF EXISTS tbu_player //
    CREATE TRIGGER tbu_player BEFORE UPDATE ON player FOR EACH ROW
    BEGIN
      SET NEW.ts=UTC_TIMESTAMP();
    END
    //

    public function down()
    {
        echo "m191025_160914_create_tbu_player_trigger cannot be reverted.\n";

        return false;
    }
    */
}
