<?php

use yii\db\Migration;

/**
 * Class m200903_174242_update_tai_player_question_trigger
 */
class m200903_174242_update_tai_player_question_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player_question}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $CREATE_SQL="CREATE TRIGGER {{%tai_player_question}} AFTER INSERT ON {{%player_question}} FOR EACH ROW
    thisBegin:BEGIN
      DECLARE local_challenge_id INT default null;
      DECLARE completed INT default null;
      DECLARE min_question,max_question, max_val, min_val DATETIME default null;

      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;

      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      CALL add_stream(NEW.player_id,'question',NEW.question_id);
      SET local_challenge_id=(SELECT challenge_id FROM question WHERE id=NEW.question_id);
      SET completed=(select true as completed FROM challenge as t left join question as t2 on t2.challenge_id=t.id LEFT JOIN player_question as t4 on t4.question_id=t2.id and t4.player_id=NEW.player_id WHERE t.id=local_challenge_id GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.question_id));
      IF completed IS NOT NULL and completed=true THEN
        SELECT min(ts),max(ts) INTO min_question,max_question FROM player_question WHERE player_id=NEW.player_id AND question_id IN (SELECT id FROM question WHERE challenge_id=local_challenge_id);
        SELECT GREATEST(max_question, min_question), LEAST(min_question, max_question) INTO max_val,min_val;
        INSERT IGNORE INTO challenge_solver (player_id,challenge_id,created_at,timer) VALUES (NEW.player_id,local_challenge_id,now(),UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
        INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'challenge',local_challenge_id,0,'','','','',now());
      END IF;

    END";

        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      return true;
    }
}
