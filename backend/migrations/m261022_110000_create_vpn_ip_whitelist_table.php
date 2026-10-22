<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vpn_ip_whitelist}}`.
 */
class m261022_110000_create_vpn_ip_whitelist_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%vpn_ip_whitelist}}', [
      'id' => $this->primaryKey(),
      'ip' => $this->integer(10)->unsigned()->notNull(),
      'mask' => $this->integer(10)->unsigned()->notNull()->defaultValue(4294967295),
      'description' => $this->text(),
      'created_at' => $this->datetime(),
      'updated_at' => $this->datetime(),
    ]);

    $this->createIndex(
      'idx-vpn_ip_whitelist-ip',
      '{{%vpn_ip_whitelist}}',
      'ip'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%vpn_ip_whitelist}}');
  }
}
