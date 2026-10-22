<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[VpnIpWhitelist]].
 *
 * @see VpnIpWhitelist
 */
class VpnIpWhitelistQuery extends \yii\db\ActiveQuery
{
  /**
   * {@inheritdoc}
   * @return VpnIpWhitelist[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return VpnIpWhitelist|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
