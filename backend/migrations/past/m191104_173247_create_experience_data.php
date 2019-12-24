<?php

use yii\db\Migration;

/**
 * Class m191104_173247_create_experience_data
 */
class m191104_173247_create_experience_data extends Migration
{
  public $experiences=[
    [ 'name'=>'New','description'=>'User just started', 'icon'=> 'default.png'],
    [ 'name'=>'Securitas','description'=>'test', 'icon'=> 'default.png'],
    [ 'name'=>'PenTester','description'=>'test', 'icon'=> 'default.png'],
    [ 'name'=>'CTFer','description'=>'test', 'icon'=> 'default.png'],
    [ 'name'=>'Hax0r','description'=>'test', 'icon'=> 'default.png'],
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $min_points=0;
      $max_points=1000;
      $step=1000;
      $dyn=1;
      $lvl=0;
      for($i=0; $i<20;$i++)
      {
        $xp['id']=intval($i+1);
        $xp['name']=sprintf("%s %d",$this->experiences[$lvl]['name'],($dyn%5));
        $xp['category']=sprintf("%s",$this->experiences[$lvl]['name']);
        $xp['description']='autogen';
        $xp['icon']='default.png';
        $xp['min_points']=intval($min_points);
        $xp['max_points']=intval($max_points+($step*($i==0 ? $i : $i+1 )));
        $this->db->createCommand()->insert('{{%experience}}', $xp)->execute();
        $max_points=$xp['max_points'];
        $min_points=$max_points+1;
        if(($dyn%4)==0) { $lvl++; $dyn=0; }
        $dyn++;
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand()->truncateTable('{{%experience}}')->execute();
    }

}
