<?php

use yii\db\Migration;

class m250903_213807_perform_team_repopulation extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->db->createCommand("CALL repopulate_all_team_streams()")->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m250903_213807_perform_team_repopulation cannot be reverted.\n";
  }

}
