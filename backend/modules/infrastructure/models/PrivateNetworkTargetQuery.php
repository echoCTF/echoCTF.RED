<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[PrivateNetworkTarget]].
 *
 * @see PrivateNetworkTarget
 */
class PrivateNetworkTargetQuery extends \yii\db\ActiveQuery
{

  public function pending_action()
  {
    return $this->andWhere([
      'and',
      [
        'or', // Conditions to match the records you want to keep
        ['is', 'ipoctet', null],
        ['ipoctet' => '0.0.0.0'],
        ['is', 'server_id', null],
        ['>', 'state', 0]
      ],
      [
        'not',
        [
          'and', // Exclude records where state = 2 and server_id is null
          ['state' => 2],
          ['is', 'server_id', null]
        ]
      ]
    ]);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetworkTarget[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetworkTarget|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
