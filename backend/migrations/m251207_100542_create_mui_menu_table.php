<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mui_menu}}`.
 */
class m251207_100542_create_mui_menu_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function up()
  {
    $this->createTable('{{%mui_menu}}', [
      'id' => $this->primaryKey(),
      'label' => $this->string(255)->notNull(),
      'url' => $this->string(255),
      'parent_id' => $this->integer(),
      'sort_order' => $this->integer()->defaultValue(0),
      'visibility' => "SET('all','guest','user','admin') NOT NULL DEFAULT 'admin'",
      'enabled' => $this->tinyInteger(1)->notNull()->defaultValue(1),
    ]);

    $this->createIndex(
      'idx-mui_menu-parent_id',
      'mui_menu',
      'parent_id'
    );

    $this->createIndex(
      'idx-mui_menu-enabled',
      'mui_menu',
      'enabled'
    );

    // optional FK if you want cascading deletes
    $this->addForeignKey(
      'fk-mui_menu-parent_id',
      'mui_menu',
      'parent_id',
      'mui_menu',
      'id',
      'CASCADE',
      'CASCADE'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function down()
  {
    $this->dropTable('{{%mui_menu}}');
  }
}
