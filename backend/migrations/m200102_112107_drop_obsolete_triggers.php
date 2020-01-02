<?php

use yii\db\Migration;

/**
 * Class m200102_112837_drop_obsolete_triggers
 */
class m200102_112107_drop_obsolete_triggers extends Migration
{


    public function up()
    {
      $TRIGGERS=[
        'tai_team',
        'tau_team',
        'tad_team',
        'tbi_team_player',
        'tbu_finding',
        'tbi_player',
        'tai_player_ip',
        'tau_player_ip',
        'tau_player_ip',
        'tai_player_mac',
        'tbi_player_question',
        'tad_team_player',
        'tbi_team',
        'tau_team_player',
        'tai_team_player',
        'tau_player',
      ];
      foreach($TRIGGERS as $trg)
      {
        $q=sprintf('DROP TRIGGER IF EXISTS {{%%%s}}',$trg);
        echo $q,"\n";
        $this->db->createCommand($q)->execute();
      }
    }

    public function down()
    {
        echo "m200102_112107_drop_obsolete_triggers cannot be reverted.\n";

    }
}
