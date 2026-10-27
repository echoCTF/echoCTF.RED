<?php

namespace app\modules\frontend\models;

use yii\db\ActiveQuery;

class PlayerQuery extends ActiveQuery
{
  public function withPresence()
  {
    return $this->addSelect([
      'player.*',
      'ifnull(memc_get(concat("ovpn:",player.id)),0) as ovpn',
      'ifnull(memc_get(concat("online:",player.id)),0) as online',
      'memc_get(concat("last_seen:",player.id)) as last_seen',
    ]);
  }

  /**
   * Count players that are online (online:<id> > 1) and on VPN (ovpn:<id> set).
   * Uses one memcached multi-get per chunk of ids instead of calling the
   * memc_get() UDF per row (and per column) inside MySQL.
   *
   * @return array{online:int,ovpn:int}
   */
  public static function presenceCounts(): array
  {
    $counts = ['online' => 0, 'ovpn' => 0];
    $mc = \Yii::$app->cache->memcache ?? null;
    if (!($mc instanceof \Memcached)) {
      // Fallback: single UDF call per row, no derived table, no player.*
      $counts['online'] = (int) Player::find()->where('memc_get(concat("online:",[[id]])) > 1')->count();
      $counts['ovpn'] = (int) Player::find()->where('ifnull(memc_get(concat("ovpn:",[[id]])),0) > 0')->count();
      return $counts;
    }
    foreach (Player::find()->select('id')->asArray()->batch(1000) as $rows) {
      $keys = [];
      foreach ($rows as $r) {
        $keys[] = 'online:' . $r['id'];
        $keys[] = 'ovpn:' . $r['id'];
      }
      $vals = $mc->getMulti($keys) ?: [];
      foreach ($vals as $k => $v) {
        if ($v === null || $v === false) continue;
        if (strncmp($k, 'online:', 7) === 0) {
          if ((float) $v > 1) $counts['online']++;
        } elseif ((float) $v > 0) {
          $counts['ovpn']++;
        }
      }
    }
    return $counts;
  }
}
