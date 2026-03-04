<?php

use yii\db\Migration;

class m260304_095934_replace_integer_url_route_matches extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $from=':\d+';
    $to=':[1-9]\d*';
    $where='%:\\\\d+%';
    $this->execute("update url_route SET source = REPLACE(source, :from, :to) WHERE source LIKE :where",[':from'=>$from,':to'=>$to,':where'=>$where]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m260304_095934_replace_integer_url_route_matches cannot be reverted.\n";
  }
}
