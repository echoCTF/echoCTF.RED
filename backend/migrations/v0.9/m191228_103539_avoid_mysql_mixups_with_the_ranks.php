<?php

use yii\db\Migration;

/**
 * Class m191228_103539_avoid_mysql_mixups_with_the_ranks
 * Due to the results being increased mysql switched to filesort on the previous procedure.
 * this has the side-effect that mysql re-parsed the query and the optimiser
 * messed up the keys being used for the ordering...
 */
class m191228_103539_avoid_mysql_mixups_with_the_ranks extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_ranks}}";

  public function up()
  {
        $CREATE_SQL="CREATE PROCEDURE calculate_ranks()
BEGIN
CREATE TEMPORARY TABLE ranking (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY;
START TRANSACTION;
  delete from player_rank;
  insert into ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 order by points desc,t.ts asc, t.player_id asc;
  insert into player_rank select * from ranking;
COMMIT;
DROP TABLE `ranking`;
END
";
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

}
