<?php

use yii\db\Migration;

/**
 * Class m201029_105417_update_challenge_icons
 */
class m201029_105417_update_challenge_icons extends Migration
{
  public $challenges=[
    [ 'id'=> 1,'icon'=> '<img align="right" src="/images/challenge/category/tutorial.svg" style="height: 58px;"/>'],
    [ 'id'=> 2,'name'=>'LFI Tutorial 101','icon'=> '<img align="right" src="/images/challenge/category/tutorial.svg" style="max-height: 58px;"/>'],
    [ 'id'=> 3,'icon'=> '<img align="right" src="/images/challenge/category/code-analysis.svg"/>'],
    [ 'id'=> 4,'icon'=> '<img align="right" src="/images/challenge/category/code-analysis.svg"/>'],
    [ 'id'=> 5,'icon'=> '<img align="right" src="/images/challenge/category/docker.svg"/>'],
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach($this->challenges as $challenge)
        $this->update("challenge", $challenge, ['id'=>$challenge['id']]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
