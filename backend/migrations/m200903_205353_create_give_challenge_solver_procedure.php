<?php

use yii\db\Migration;

/**
 * Class m200903_205353_create_give_challenge_solver_procedure
 */
class m200903_205353_create_give_challenge_solver_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%give_challenge_solver}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%give_challenge_solver}} (IN pid INT, IN tid INT, IN ttimer INT)
  BEGIN
    DECLARE completed INT default null;
    DECLARE min_question,max_question, max_val, min_val DATETIME default null;

    SET completed=(select true as completed FROM challenge as t left join question as t2 on t2.challenge_id=t.id LEFT JOIN player_question as t4 on t4.question_id=t2.id and t4.player_id=pid WHERE t.id=tid GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.question_id));
    IF completed IS NOT NULL and completed=true THEN
      SELECT min(ts),max(ts) INTO min_question,max_question FROM player_question WHERE player_id=pid AND question_id IN (SELECT id FROM question WHERE challenge_id=tid);
      SELECT GREATEST(max_question, min_question), LEAST(min_question, max_question) INTO max_val,min_val;
      INSERT IGNORE INTO challenge_solver (player_id,challenge_id,created_at,timer) VALUES (pid,tid,max_val,UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
      IF ROW_COUNT()>0 THEN
        INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (pid,'challenge',tid,0,'','','','',max_val);
      END IF;
    END IF;

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
