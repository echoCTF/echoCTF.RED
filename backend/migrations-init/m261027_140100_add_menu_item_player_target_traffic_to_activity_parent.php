<?php

use yii\db\Migration;

class m261027_140100_add_menu_item_player_target_traffic_to_activity_parent extends Migration
{
  public $parentLabel = '<i class="bi bi-bar-chart-fill"></i> Activity';
  public $itemLabel = 'Player Target Traffic';
  public $itemUrl = '/activity/player-target-traffic/index';

  public function safeUp()
  {
    $parentId = (new \yii\db\Query())
      ->select('id')
      ->from('mui_menu')
      ->where(['like', 'label', $this->parentLabel])
      ->andWhere(['parent_id' => null])
      ->scalar();

    if (!$parentId) {
      throw new \yii\base\Exception('Parent menu "' . $this->parentLabel . '" not found, fix lookup.');
    }

    $sortOrder = (new \yii\db\Query())
      ->from('mui_menu')
      ->where(['parent_id' => $parentId])
      ->count();

    $this->upsert('mui_menu', [
      'label' => $this->itemLabel,
      'url' => $this->itemUrl,
      'visibility' => 'admin',
      'parent_id' => $parentId,
      'sort_order' => $sortOrder,
      'enabled' => 1,
    ]);
  }

  public function safeDown()
  {
    $this->delete('mui_menu', [
      'url' => $this->itemUrl,
    ]);
  }
}
