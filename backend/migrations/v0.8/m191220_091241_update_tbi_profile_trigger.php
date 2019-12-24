<?php

use yii\db\Migration;

/**
 * Class m191220_091241_update_tbi_profile_trigger
 */
class m191220_091241_update_tbi_profile_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbi_profile}}";

    public function up()
    {
    $CREATE_SQL="CREATE TRIGGER {{%tbi_profile}} BEFORE INSERT ON {{%profile}} FOR EACH ROW
  BEGIN
  IF NEW.id is NULL or NEW.id<10000000 THEN
    REPEAT
        SET NEW.id=round(rand()*10000000);
    UNTIL (SELECT id FROM profile WHERE id=NEW.id) IS NULL
    END REPEAT;
  END IF;
  IF NEW.created_at IS NULL or NEW.updated_at IS NULL THEN
    SET NEW.created_at=NOW();
    SET NEW.updated_at=NOW();
  END IF;
  IF NEW.bio IS NULL THEN
    SET NEW.bio='No bio...';
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
