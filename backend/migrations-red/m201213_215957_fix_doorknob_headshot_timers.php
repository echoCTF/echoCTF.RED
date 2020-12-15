<?php

use yii\db\Migration;

/**
 * Class m201213_215957_fix_doorknob_headshot_timers
 */
class m201213_215957_fix_doorknob_headshot_timers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $cmd="UPDATE headshot SET timer=timer-(5*(24*60*60)) WHERE target_id=34 AND player_id=1227";
      $this->db->createCommand($cmd)->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201213_215957_fix_doorknob_headshot_timers cannot be reverted.\n";
    }
}
