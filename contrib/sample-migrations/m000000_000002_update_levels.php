<?php

use yii\db\Migration;

/**
 * Class m000000_000002_update_levels
 */
class m000000_000002_update_levels extends Migration
{
  public $levels = [
    'newbie' => ['min_points' => 0, 'max_points' => 33010],
    'CTFer' => ['min_points' => (33010 * 1) + 1, 'max_points' => 33010 * 2],
    'hax0r' => ['min_points' => (33010 * 2) + 1, 'max_points' => 33010 * 3],
    '1337' => ['min_points' => (33010 * 3) + 1, 'max_points' => 33010 * 4],
    '13373r3r' => ['min_points' => (33010 * 4) + 1, 'max_points' => 33010 * 10],
  ];
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $id = 1;
    $this->truncateTable('experience');
    foreach ($this->levels as $name => $lvl) {
      $this->upsert('experience', ['id' => $id, 'name' => $name, 'category' => $name, 'description' => $name, 'icon' => 'default.png', 'min_points' => $lvl['min_points'], 'max_points' => $lvl['max_points']]);
      $id++;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}
}
