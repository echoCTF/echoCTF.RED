<?php

use yii\db\Migration;

/**
 * Class m191203_120445_drop_tbu_player_trigger
 */
class m191203_120445_drop_tbu_player_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbu_player}}";
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }

    public function down()
    {
        echo "m191203_120445_drop_tbu_player_trigger cannot be reverted.\n";

    }

}
