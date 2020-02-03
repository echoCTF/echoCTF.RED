<?php

use yii\db\Migration;

/**
 * Class m200203_125056_create_and_add_lfi_and_dolph_badges
 */
class m200203_125056_create_and_add_lfi_and_dolph_badges extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->insert('badge',['name'=>'Developer of LFI Tutorial and dolph','pubname'=>'<i class="fab fa-galactic-senate text-warning"></i>','id'=>3,'points'=>6350]);
      $this->insert('player_badge',['player_id'=>3,'badge_id'=>3]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('player_badge',['player_id'=>3,'badge_id'=>3]);
      $this->delete('badge',['id'=>3]);

    }
}
