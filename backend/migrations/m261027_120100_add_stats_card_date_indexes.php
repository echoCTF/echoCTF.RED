<?php

use yii\db\Migration;

/**
 * Adds date-column indexes used by the dashboard stats cards
 * (today / yesterday counts) on player.created and player_target_help.created_at.
 * stream.ts is already the leading column of `query-index`.
 */
class m261027_120100_add_stats_card_date_indexes extends Migration
{
  public function safeUp()
  {
    $this->createIndex('idx-player-created', '{{%player}}', 'created');
    $this->createIndex('idx-player_target_help-created_at', '{{%player_target_help}}', 'created_at');
  }

  public function safeDown()
  {
    $this->dropIndex('idx-player-created', '{{%player}}');
    $this->dropIndex('idx-player_target_help-created_at', '{{%player_target_help}}');
  }
}
