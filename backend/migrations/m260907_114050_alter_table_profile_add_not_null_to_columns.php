<?php

use yii\db\Migration;

class m260907_114050_alter_table_profile_add_not_null_to_columns extends Migration
{
  public function safeUp()
  {
    $this->alterColumn('profile', 'country', $this->string(3)->notNull()->defaultValue('UNK')->comment('Country code (eg GR)'));
    $this->alterColumn('profile', 'avatar', $this->string(255)->notNull()->defaultValue('default.png')->comment('Profile avatar'));
    $this->alterColumn('profile', 'discord', $this->string(255)->notNull()->defaultValue('')->comment('Discord handle (eg  @username#1234)'));
    $this->alterColumn('profile', 'twitter', $this->string(255)->notNull()->defaultValue('')->comment('Twitter handle (eg @echoCTF)'));
    $this->alterColumn('profile', 'github', $this->string(255)->notNull()->defaultValue('')->comment('Github handle (eg echoCTF)'));
  }

  public function safeDown()
  {
    $this->alterColumn('profile', 'country', $this->string(3)->null()->defaultValue('UNK')->comment('Country code (eg GR)'));
    $this->alterColumn('profile', 'avatar', $this->string(255)->null()->defaultValue('default.png')->comment('Profile avatar'));
    $this->alterColumn('profile', 'discord', $this->string(255)->null()->defaultValue('')->comment('Discord handle (eg  @username#1234)'));
    $this->alterColumn('profile', 'twitter', $this->string(255)->null()->defaultValue('')->comment('Twitter handle (eg @echoCTF)'));
    $this->alterColumn('profile', 'github', $this->string(255)->null()->defaultValue('')->comment('Github handle (eg echoCTF)'));
  }
}
