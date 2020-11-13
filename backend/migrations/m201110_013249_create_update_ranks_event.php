<?php

use yii\db\Migration;

/**
 * Class m201110_013249_create_update_ranks_event
 */
class m201110_013249_create_update_ranks_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%update_ranks}}";
  public $CREATE_SQL="CREATE EVENT {{%update_ranks}} ON SCHEDULE EVERY 30 SECOND ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    ALTER EVENT {{%update_ranks}} DISABLE;
    call calculate_ranks();
    call calculate_country_rank();
    call calculate_team_ranks();
    ALTER EVENT {{%update_ranks}} ENABLE;
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
