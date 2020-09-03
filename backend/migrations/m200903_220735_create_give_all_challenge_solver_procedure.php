<?php

use yii\db\Migration;

/**
 * Class m200903_220735_create_give_all_challenge_solver_procedure
 */
class m200903_220735_create_give_all_challenge_solver_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%give_all_challenge_solver}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%give_all_challenge_solver}}()
  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE lpid,lcid,local_challenge_id INT DEFAULT 0;
    DECLARE cur1 CURSOR FOR SELECT t.player_id,t2.challenge_id FROM player_question t LEFT JOIN question AS t2 ON t2.id=t.question_id GROUP BY t.player_id,t2.challenge_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur1;
    read_loop: LOOP
      FETCH cur1 INTO lpid,lcid;
      IF done THEN
        LEAVE read_loop;
      END IF;
      CALL give_challenge_solver(lpid,lcid,0);
    END LOOP;

    CLOSE cur1;
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
