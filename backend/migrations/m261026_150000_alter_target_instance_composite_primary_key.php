<?php

use yii\db\Migration;

/**
 * Allow players to own more than one target instance (one per target).
 *
 * The primary key of `target_instance` changes from (`player_id`) to
 * (`player_id`,`target_id`). The new key still starts with `player_id`, so the
 * existing `fk-target_instance-player_id` foreign key keeps a usable index.
 * Both keys are swapped in a single ALTER so the foreign key is never left
 * without an index.
 */
class m261026_150000_alter_target_instance_composite_primary_key extends Migration
{
  public function safeUp()
  {
    $this->execute('ALTER TABLE {{%target_instance}} DROP PRIMARY KEY, ADD PRIMARY KEY ([[player_id]], [[target_id]])');
  }

  public function safeDown()
  {
    // Keep only the oldest instance of each player so that player_id becomes unique again.
    $this->execute('DELETE t1 FROM {{%target_instance}} t1
      JOIN {{%target_instance}} t2
        ON t1.player_id = t2.player_id
       AND (t1.created_at > t2.created_at OR (t1.created_at = t2.created_at AND t1.target_id > t2.target_id))');
    $this->execute('ALTER TABLE {{%target_instance}} DROP PRIMARY KEY, ADD PRIMARY KEY ([[player_id]])');
  }
}
