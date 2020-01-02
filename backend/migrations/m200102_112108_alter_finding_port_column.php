<?php

use yii\db\Migration;

/**
 * Class m200102_112108_alter_finding_port_column
 */
class m200102_112108_alter_finding_port_column extends Migration
{
    public function up()
    {
      $this->alterColumn('{{%finding}}','port',$this->smallInteger(5).' UNSIGNED DEFAULT 0');
    }

    public function down()
    {
        echo "m200102_112108_alter_finding_port_column cannot be reverted.\n";
    }

}
