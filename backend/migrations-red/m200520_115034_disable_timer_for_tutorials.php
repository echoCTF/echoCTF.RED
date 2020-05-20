<?php

use yii\db\Migration;

/**
 * Class m200520_115034_disable_timer_for_tutorials
 */
class m200520_115034_disable_timer_for_tutorials extends Migration
{
  public $root_IDS=[
      11,
      12,
      23,
      26,
      29
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

      $this->update("target", ["timer"=>0], ['in', 'id', $this->root_IDS]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update("target", ["timer"=>1], ['in', 'id', $this->root_IDS]);
    }

}
