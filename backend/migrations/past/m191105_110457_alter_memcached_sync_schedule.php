<?php

use yii\db\Migration;

/**
 * Class m191105_110457_alter_memcached_sync_schedule
 */
class m191105_110457_alter_memcached_sync_schedule extends Migration
{
  public $ALTER_SQL="ALTER EVENT {{%memcached_sync}} ON SCHEDULE EVERY 10 SECOND";

    public function up()
    {
        $this->db->createCommand($this->ALTER_SQL)->execute();
    }

    public function down()
    {
      return true;
    }
  }
