<?php

use yii\db\Migration;

/**
 * Class m191118_082340_insert_default_networks
 */
class m191118_082340_insert_default_networks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->insert('network', [
        'name' => 'Simpsons Network',
        'description'=> 'The Simpsons network',
        'public' => true
      ]);
      $this->insert('network', [
        'name' => 'Southpark Network',
        'description'=> 'The Southpark network',
        'public' => true
      ]);
      $this->insert('network', [
        'name' => 'CVE Network',
        'description'=> 'The CVE network',
        'public' => true
      ]);
      $this->insert('network', [
        'name' => 'Tutorial Network',
        'description'=> 'The Tutorials network',
        'public' => true
      ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('network', ['id' => 4]);
      $this->delete('network', ['id' => 3]);
      $this->delete('network', ['id' => 2]);
      $this->delete('network', ['id' => 1]);
    }
}
