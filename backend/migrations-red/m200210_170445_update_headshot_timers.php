<?php

use yii\db\Migration;
use yii\db\Query;
/**
 * Class m200210_170445_update_headshot_timers
 */
class m200210_170445_update_headshot_timers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      // update status field for all users
      foreach((new Query)->from('headshot')->where(['timer'=>0])->each() as $headshot) {
        $cmd=sprintf("CALL time_headshot(%d,%d)",$headshot['player_id'],$headshot['target_id']);
        echo $cmd,';',"\n";
        $this->db->createCommand($cmd)->execute();
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
