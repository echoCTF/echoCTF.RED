<?php

use yii\db\Migration;

class m260912_093306_add_menu_item_ws_token_history_to_activity_parent extends Migration
{
  public function safeUp()
  {
    $parentId = (new \yii\db\Query())
      ->select('id')
      ->from('mui_menu')
      ->where(['label' => '<i class="bi bi-bar-chart-fill"></i> Activity', 'parent_id' => null])
      ->scalar();

    $sortOrder = (new \yii\db\Query())
      ->from('mui_menu')
      ->where(['parent_id' => $parentId])
      ->count();

    $this->upsert('mui_menu', [
      'label' => 'WS Token History',
      'url' => '/activity/ws-token-history/index',
      'visibility' => 'admin',
      'parent_id' => $parentId,
      'sort_order' => $sortOrder,
      'enabled' => 1,
    ]);
  }

  public function safeDown()
  {
    $this->delete('mui_menu', [
      'url' => '/activity/ws-token-history/index',
    ]);
  }
}
