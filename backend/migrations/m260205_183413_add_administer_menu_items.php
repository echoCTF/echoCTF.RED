<?php

use yii\db\Migration;

class m260205_183413_add_administer_menu_items extends Migration
{
  public $items = [
    [
      'id' => 147,
      'label' => '<i class="bi bi-tools"></i> Administer',
      'url' => ['/administer'],
      'visibility' => 'admin',
      'items' => [
        ['id' => 148, 'label' => 'Main', 'url' => ['/administer/default/index'], 'visibility' => 'admin',],
        ['id' => 149, 'label' => 'Events', 'url' => ['/administer/events/index'], 'visibility' => 'admin',],
      ],
    ],

  ];
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $root = 8;
    foreach ($this->items as $menu) {
      $this->upsert('mui_menu', ['id' => $menu['id'], 'label' => $menu['label'], 'url' => $menu['url'][0], 'visibility' => $menu['visibility'], 'sort_order' => $root++, 'enabled' => intval(@$menu['enabled'] ?? 1)]);
      $id = Yii::$app->db->getLastInsertID();
      $child = 0;
      foreach ($menu['items'] as $item) {
        if (is_array($item))
          $this->upsert('mui_menu', ['id' => $menu['id'], 'label' => $item['label'], 'url' => $item['url'][0], 'visibility' => $item['visibility'], 'parent_id' => $id, 'sort_order' => $child++, 'enabled' => intval(@$item['enabled'] ?? (@$menu['enabled'] ?? 1))]);
        else
          $this->upsert('mui_menu', ['id' => $menu['id'], 'label' => $item, 'visibility' => 'admin', 'parent_id' => $id, 'sort_order' => $child++]);
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m260205_183413_add_administer_menu_items cannot be reverted.\n";
  }
}
