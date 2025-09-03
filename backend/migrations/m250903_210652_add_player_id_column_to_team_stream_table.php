<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%team_stream}}`.
 */
class m250903_210652_add_player_id_column_to_team_stream_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->addColumn('{{%team_stream}}', 'stream_id', $this->bigInteger(20)->unsigned()->after('points'));
    $this->addColumn('{{%team_stream}}', 'player_id', $this->integer()->unsigned()->after('points'));
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropColumn('{{%team_stream}}', 'player_id');
    $this->dropColumn('{{%team_stream}}', 'stream_id');
  }
}
