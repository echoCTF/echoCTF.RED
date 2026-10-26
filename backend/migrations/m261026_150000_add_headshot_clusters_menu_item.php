<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Adds the Moderation / Headshot Clusters menu item.
 */
class m261026_150000_add_headshot_clusters_menu_item extends Migration
{
  const URL = '/moderation/default/headshot-clusters';

  public function safeUp()
  {
    $parent_id = (new Query())->select('id')->from('mui_menu')
      ->where(['url' => '/moderation/default/index', 'parent_id' => null])
      ->scalar($this->db);

    if ($parent_id === false) {
      echo "    > Moderation menu not found, skipping menu item.\n";
      return true;
    }

    $exists = (new Query())->from('mui_menu')->where(['url' => self::URL])->exists($this->db);
    if ($exists) {
      return true;
    }

    $sort_order = (int) (new Query())->from('mui_menu')->where(['parent_id' => $parent_id])->max('sort_order', $this->db) + 1;

    $this->insert('mui_menu', [
      'label' => 'Headshot Clusters',
      'url' => self::URL,
      'parent_id' => $parent_id,
      'sort_order' => $sort_order,
      'visibility' => 'user,admin',
      'enabled' => 1,
    ]);
  }

  public function safeDown()
  {
    $this->delete('mui_menu', ['url' => self::URL]);
  }
}
