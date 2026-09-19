<?php

use yii\db\Migration;

class m260912_233058_add_menu_item_player_bandwidth_to_activity_parent extends Migration
{
  public $parentLabel = '<i class="bi bi-bar-chart-fill"></i> Activity';
  public $itemLabel = 'Player Bandwidth';
  public $itemUrl = '/activity/player-bandwidth/index';

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
