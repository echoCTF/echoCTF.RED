<?php

use yii\db\Migration;

/**
 * Class m241110_214630_from_v0_25_0_to_v1_0_0
 */
class m241110_214630_from_v0_25_0_to_v1_0_0 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $is_existing=$this->db->createCommand("SELECT COUNT(*) FROM init_data")->queryScalar();
      if($is_existing>0){
        $this->upsert('init_data',['version'=>'m241108_100648_populate_default_sysconfig_keys','apply_time'=>time()]);
        // add missing url routes
      }
      $this->upsert('sysconfig',['id'=>'platform_version','val'=>'v1.0.0']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      echo "Reversing version only!";
      $this->upsert('sysconfig',['id'=>'platform_version','val'=>'v0.25.0']);
    }
}
