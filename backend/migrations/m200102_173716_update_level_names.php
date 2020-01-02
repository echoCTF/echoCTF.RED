<?php

use yii\db\Migration;

/**
 * Class m200102_173716_update_level_names
 */
class m200102_173716_update_level_names extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->update('experience',['name'=>'Newcomer'],['id'=>1]);
      $this->update('experience',['name'=>'Webuser'],['id'=>2]);
      $this->update('experience',['name'=>'Trainee'],['id'=>3]);
      $this->update('experience',['name'=>'User'],['id'=>4]);

      $this->update('experience',['name'=>'Junior Securitas'],['id'=>5]);
      $this->update('experience',['name'=>'Securitas'],['id'=>6]);
      $this->update('experience',['name'=>'Senior Securitas'],['id'=>7]);
      $this->update('experience',['name'=>'Master Securitas'],['id'=>8]);

      $this->update('experience',['name'=>'Junior PenTester'],['id'=>9]);
      $this->update('experience',['name'=>'PenTester'],['id'=>10]);
      $this->update('experience',['name'=>'Senior PenTester'],['id'=>11]);
      $this->update('experience',['name'=>'Master PenTester'],['id'=>12]);

      $this->update('experience',['name'=>'Junior CTFer'],['id'=>13]);
      $this->update('experience',['name'=>'CTFer'],['id'=>14]);
      $this->update('experience',['name'=>'Senior CTFer'],['id'=>15]);
      $this->update('experience',['name'=>'Master CTFer'],['id'=>16]);

      $this->update('experience',['name'=>'Junior Hax0r'],['id'=>17]);
      $this->update('experience',['name'=>'Hax0r'],['id'=>18]);
      $this->update('experience',['name'=>'Senior Hax0r'],['id'=>19]);
      $this->update('experience',['name'=>'Master Hax0r'],['id'=>20]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200102_173716_update_level_names cannot be reverted.\n";

    }

}
