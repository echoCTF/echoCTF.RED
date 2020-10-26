<?php

use yii\db\Migration;

/**
 * Class m201026_102939_update_disabled_routes
 */
class m201026_102939_update_disabled_routes extends Migration
{
  public $routes=[
    '/network/default/index',
    '/help/rule/index',
    '/site/changelog',
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach($this->routes as $val)
        $this->db->createCommand("REPLACE disabled_route SET route=:route")->bindValue(':route',$val)->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

}
