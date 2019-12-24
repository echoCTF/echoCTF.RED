<?php

use yii\db\Migration;

/**
 * Class m191118_083200_insert_default_network_targets
 */
class m191118_083200_insert_default_network_targets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      // Simpsons Targets
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 2,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 3,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 4,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 5,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 6,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 7,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 8,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 9,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 10,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 13,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 14,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);
      $this->insert('network_target', ['network_id' => 1,'target_id'=> 16,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);

      // Southpark
      $this->insert('network_target', ['network_id' => 2,'target_id'=> 15,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);

      // CVE
      $this->insert('network_target', ['network_id' => 3,'target_id'=> 12,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);

      // Tutorials
      $this->insert('network_target', ['network_id' => 4,'target_id'=> 11,'created_at'=>new \yii\db\Expression('now()'),'updated_at'=>new \yii\db\Expression('now()')]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      // Simpsons Targets
      $this->delete('network_target', ['network_id' => 1]);

      // Southpark
      $this->delete('network_target', ['network_id' => 2]);

      // CVE
      $this->delete('network_target', ['network_id' => 3]);

      // Tutorials
      $this->delete('network_target', ['network_id' => 4]);
    }

}
