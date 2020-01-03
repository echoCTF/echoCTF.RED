<?php

use yii\db\Migration;

/**
 * Class m200103_085150_add_hint_for_new_users
 */
class m200103_085150_add_hint_for_new_users extends Migration
{
  public $record=[
      'id'=>-1,
      'title'=>'Welcome to echoCTF.RED, checkout <code>tutorial-101</code> and <code>lfi-tutorial</code> hosts to get started',
      'player_type'=>'offense',
      'message'=>'Welcome to echoCTF.RED, get started with the tutorial hosts to familiarize your self with the platform',
      'category'=>'easy_points',
      'active'=>1,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->insert('hint',$this->record);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('hint',$this->record);
    }

}
