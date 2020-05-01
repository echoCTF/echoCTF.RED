<?php

use yii\db\Migration;

/**
 * Class m200210_153450_update_basic_treasure_categories
 */
class m200210_153450_update_basic_treasure_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $env_IDS=[2, 11, 25, 32, 43, 50, 57, 64, 74, 80, 84, 88, 99, 106, 116, 124, 129, 136, 145, 162, 171, 175, 185, 197, 203, 209, 213];
      $root_IDS=[1, 10, 24, 31, 42, 49, 56, 63, 73, 79, 83, 87, 98, 105, 115, 123, 128, 135, 144, 161, 170, 174, 184, 196, 202, 208, 212];
      $shadow_IDS=[8, 12, 36, 45, 54, 65, 75, 81, 85, 89, 100, 114, 117, 125, 130, 137, 147, 163, 172, 176, 186, 198, 204, 210, 214];
      $passwd_IDS=[9, 13, 37, 46, 55, 58, 66, 76, 82, 86, 90, 101, 107, 118, 126, 131, 138, 148, 164, 173, 177, 187, 199, 205, 211, 215];
      $user_IDS=[122];
      $this->update("treasure", ["category"=>"root"], ['in', 'id', $root_IDS]);
      $this->update("treasure", ["category"=>"user"], ['in', 'id', $user_IDS]);
      $this->update("treasure", ["category"=>"env"], ['in', 'id', $env_IDS]);
      $this->update("treasure", ["category"=>"system"], ['in', 'id', $shadow_IDS]);
      $this->update("treasure", ["category"=>"system"], ['in', 'id', $passwd_IDS]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
