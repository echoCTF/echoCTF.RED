<?php

use yii\db\Migration;

/**
 * Class m200204_072552_update_network_targets
 */
class m200204_072552_update_network_targets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->insert('network_target',['network_id'=>1,'target_id'=>20]); // Simpsons, dolph
      $this->insert('network_target',['network_id'=>2,'target_id'=>19]); // Southpark, chef
      $this->insert('network_target',['network_id'=>2,'target_id'=>25]); // Southpark, craig
      $this->insert('network_target',['network_id'=>2,'target_id'=>21]); // Southpark, elementary
      $this->insert('network_target',['network_id'=>2,'target_id'=>22]); // Southpark, harrison
      $this->insert('network_target',['network_id'=>2,'target_id'=>30]); // Southpark, pcprincipal
      $this->insert('network_target',['network_id'=>2,'target_id'=>17]); // Southpark, terrance
      $this->insert('network_target',['network_id'=>2,'target_id'=>27]); // Southpark, thecoon
      $this->insert('network_target',['network_id'=>2,'target_id'=>24]); // Southpark, tweek
      $this->insert('network_target',['network_id'=>2,'target_id'=>28]); // Southpark, wendy
      $this->insert('network_target',['network_id'=>3,'target_id'=>18]); // CVE, CVE-2018-11776
      $this->insert('network_target',['network_id'=>3,'target_id'=>23]); // CVE, CVE-2019-1010174
      $this->insert('network_target',['network_id'=>3,'target_id'=>29]); // CVE, CVE-2020-7247
      $this->insert('network_target',['network_id'=>4,'target_id'=>26]); // Tutorial, lfi-tutorial
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('network_target',['network_id'=>1,'target_id'=>20]); // Simpsons, dolph
      $this->delete('network_target',['network_id'=>2,'target_id'=>19]); // Southpark, chef
      $this->delete('network_target',['network_id'=>2,'target_id'=>25]); // Southpark, craig
      $this->delete('network_target',['network_id'=>2,'target_id'=>21]); // Southpark, elementary
      $this->delete('network_target',['network_id'=>2,'target_id'=>22]); // Southpark, harrison
      $this->delete('network_target',['network_id'=>2,'target_id'=>30]); // Southpark, pcprincipal
      $this->delete('network_target',['network_id'=>2,'target_id'=>17]); // Southpark, terrance
      $this->delete('network_target',['network_id'=>2,'target_id'=>27]); // Southpark, thecoon
      $this->delete('network_target',['network_id'=>2,'target_id'=>24]); // Southpark, tweek
      $this->delete('network_target',['network_id'=>2,'target_id'=>28]); // Southpark, wendy
      $this->delete('network_target',['network_id'=>3,'target_id'=>18]); // CVE, CVE-2018-11776
      $this->delete('network_target',['network_id'=>3,'target_id'=>23]); // CVE, CVE-2019-1010174
      $this->delete('network_target',['network_id'=>3,'target_id'=>29]); // CVE, CVE-2020-7247
      $this->delete('network_target',['network_id'=>4,'target_id'=>26]); // Tutorial, lfi-tutorial

    }

}
