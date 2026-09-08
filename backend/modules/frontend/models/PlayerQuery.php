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
}
