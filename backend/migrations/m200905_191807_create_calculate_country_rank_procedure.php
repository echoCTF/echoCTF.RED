<?php

use yii\db\Migration;

/**
 * Class m200905_191807_create_calculate_country_rank_procedure
 */
class m200905_191807_create_calculate_country_rank_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_country_rank}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_country_rank}} ()
  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE ccode VARCHAR(3);
    DECLARE cur1 CURSOR FOR SELECT DISTINCT country FROM profile;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    CREATE TEMPORARY TABLE {{%country_ranking}} (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY;
    OPEN cur1;
    read_loop: LOOP
      FETCH cur1 INTO ccode;
      IF done THEN
        LEAVE read_loop;
      END IF;
      START TRANSACTION;
        delete from player_country_rank WHERE country=ccode;
        insert into country_ranking SELECT NULL,t.player_id FROM player_score AS t
          LEFT JOIN player AS t2 ON t.player_id=t2.id
          LEFT JOIN profile AS t3 ON t.player_id=t3.player_id
          WHERE t2.active=1 AND t3.country=ccode ORDER BY points DESC,t.ts ASC, t.player_id ASC;
        insert into player_country_rank select *,ccode from country_ranking;
      COMMIT;
      TRUNCATE country_ranking;
    END LOOP;
    CLOSE cur1;
    DROP TABLE country_ranking;
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
