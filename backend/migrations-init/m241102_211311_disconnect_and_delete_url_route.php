<?php

use yii\db\Migration;

/**
 * Class m241102_211311_disconnect_url_route
 */
class m241102_211311_disconnect_and_delete_url_route extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $this->upsert('url_route',['source'=>'profile/disconnect','destination'=>'profile/disconnect','weight'=>341],true);
    $this->upsert('url_route',['source'=>'profile/delete','destination'=>'profile/delete','weight'=>342],true);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->delete('url_route',['source'=>'profile/disconnect']);
    $this->delete('url_route',['source'=>'profile/delete']);
  }
}
