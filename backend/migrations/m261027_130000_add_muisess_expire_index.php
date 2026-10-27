<?php

use yii\db\Migration;

/**
 * Adds an index on muisess.expire so session garbage collection
 * (DELETE FROM muisess WHERE expire < NOW) avoids a full table scan.
 */
class m261027_130000_add_muisess_expire_index extends Migration
{
  public function safeUp()
  {
    $this->createIndex('idx-muisess-expire', '{{%muisess}}', 'expire');
  }

  public function safeDown()
  {
    $this->dropIndex('idx-muisess-expire', '{{%muisess}}');
  }
}
