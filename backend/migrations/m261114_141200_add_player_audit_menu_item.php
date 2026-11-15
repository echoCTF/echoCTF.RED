<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Adds the Frontend / Players Audit menu item.
 */
class m261114_141200_add_player_audit_menu_item extends Migration
{
  const URL = '/frontend/player-audit/index';

  public function safeUp()
  {
    $parent_id = (new Query())->select('id')->from('mui_menu')
      ->where(['url' => '/frontend/default/index', 'parent_id' => null])
      ->scalar($this->db);

    if ($parent_id === false) {
      echo "    > Frontend menu not found, skipping menu item.\n";
      return true;
    }

    $exists = (new Query())->from('mui_menu')->where(['url' => self::URL])->exists($this->db);
    if ($exists) {
      return true;
    }

    // place it after Player Relations, or at the end when that is missing
    $after = (new Query())->select('sort_order')->from('mui_menu')
      ->where(['url' => '/frontend/player-relation/index', 'parent_id' => $parent_id])
      ->scalar($this->db);
    if ($after === false) {
      $sort_order = (int) (new Query())->from('mui_menu')->where(['parent_id' => $parent_id])->max('sort_order', $this->db) + 1;
    } else {
      $sort_order = (int) $after + 1;
      $this->update('mui_menu', ['sort_order' => new \yii\db\Expression('sort_order+1')], ['and', ['parent_id' => $parent_id], ['>=', 'sort_order', $sort_order]]);
    }

    $this->insert('mui_menu', [
      'label' => 'Players Audit',
      'url' => self::URL,
      'parent_id' => $parent_id,
      'sort_order' => $sort_order,
      'visibility' => 'user',
      'enabled' => 1,
    ]);
  }

  public function safeDown()
  {
    $this->delete('mui_menu', ['url' => self::URL]);
  }
}
