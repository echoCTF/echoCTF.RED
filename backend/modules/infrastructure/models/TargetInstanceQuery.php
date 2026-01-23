<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetInstance]].
 *
 * @see TargetInstance
 */
class TargetInstanceQuery extends \yii\db\ActiveQuery
{
  /**
   * Filters TargetInstances to those that are active
   * and whose team has at least one approved member with vpn_local_address != 0
   */
  public function withApprovedMemberHeartbeat()
  {
    return $this
      // join to the team_instance player with teamPlayer
      ->innerJoin(['tp' => 'team_player'], 'target_instance.player_id = tp.player_id AND tp.approved = 1')
      // join to the team
      ->innerJoin(['t' => 'team'], 'tp.team_id = t.id')
      // join to approved members of the team
      ->innerJoin(['am' => 'team_player'], 'am.team_id = t.id AND am.approved = 1')
      // join approved member's last
      ->innerJoin(['al' => 'player_last'], 'al.id = am.player_id and al.vpn_local_address is not null')
      // ensure the approved member has a vpn_local_address
      ->distinct();
  }

  public function active()
  {
    return $this->andWhere('target_instance.[[ip]] IS NOT NULL')->andWhere('target_instance.[[reboot]]!=2');
  }

  public function last_updated(int $seconds_ago = 1)
  {
    return $this->andWhere(['<', 'target_instance.[[updated_at]]', new \yii\db\Expression("NOW() - INTERVAL $seconds_ago SECOND")]);
  }

  public function pending_action(int $seconds_ago = 1)
  {
    return $this->addSelect([
      'target_instance.*',
      'reboot' => new \yii\db\Expression(
        "IF(target_instance.updated_at < (NOW() - INTERVAL :seconds SECOND), 2, target_instance.reboot)",
        [':seconds' => $seconds_ago]
      ),
    ])
      ->andWhere([
        'or',
        ['target_instance.ip' => null],
        ['>', 'target_instance.reboot', 0],
        ['<', 'target_instance.updated_at', new \yii\db\Expression("NOW() - INTERVAL $seconds_ago SECOND")]
      ]);
  }


  /**
   * {@inheritdoc}
   * @return TargetInstance[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return TargetInstance|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
