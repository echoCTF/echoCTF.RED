<?php

use yii\db\Migration;

/**
 * Class m201213_211008_create_tbi_player_treasure_trigger
 */
class m201213_211008_create_tbi_player_treasure_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbi_player_treasure}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tbi_player_treasure}} BEFORE INSERT ON {{%player_treasure}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM treasure WHERE id=NEW.treasure_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
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
