<?php

use yii\db\Migration;

/**
 * Class m200102_110922_drop_obsolete_routines
 */
class m200102_110922_drop_obsolete_routines extends Migration
{
    public function up()
    {
      $tables=[
        'add_player_team',
        'add_player',
        'populate_bridge_rules',
        'generate_match_finding',
        'player_hint_register',
        'player_hint_treasure',
        'give_player_badges',
        'player_findingsNtreasures_vs_badges',
        'add_hint',
        'generate_combo_game_data',
      ];
      foreach($tables as $tbl)
      {
        printf("Droping routine {{%%%s}}\n",$tbl);
        $q=sprintf('DROP PROCEDURE IF EXISTS {{%%%s}}',$tbl);
        $this->db->createCommand($q)->execute();
      }

    }

    public function down()
    {
      echo "m200102_110922_drop_obsolete_routines cannot be reverted.\n";
    }

}
