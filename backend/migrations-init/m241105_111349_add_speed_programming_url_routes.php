<?php

use yii\db\Migration;

/**
 * Class m241105_111349_add_speed_programming_url_routes
 */
class m241105_111349_add_speed_programming_url_routes extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('url_route', ['source' => 'speed', 'destination' => 'speedprogramming/default/index'], true);
    $this->upsert('url_route', ['source' => 'speed/<id:\d+>', 'destination' => 'speedprogramming/default/view'], true);
    $this->upsert('url_route', ['source' => 'speed/<id:\d+>/answer', 'destination' => 'speedprogramming/default/answer'], true);
    $this->upsert('sysconfig', ['id' => 'module_speedprogramming_disabled', 'val' => 1], true);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->delete('url_route', ['source' => 'speed',]);
    $this->delete('url_route', ['source' => 'speed/<id:\d+>']);
    $this->delete('url_route', ['source' => 'speed/<id:\d+>/answer']);
  }
}
