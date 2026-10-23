<?php

use yii\db\Migration;

class m261022_110200_add_menu_item_vpn_ip_whitelist_to_settings_parent extends Migration
{
  public $parentLabel = '<i class="bi bi-house-gear-fill"></i> Settings';
  public $itemLabel = 'VPN IP Whitelist';
  public $itemUrl = '/settings/vpn-ip-whitelist/index';

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
