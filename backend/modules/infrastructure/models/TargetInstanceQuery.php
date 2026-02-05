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
   * Filters target instances that are active and have at least one approved team member
   * with a non-null VPN local address.
   *
   * Joins:
   * - `team_player` (`tp`) to match the instance's player and check approval.
   * - `team` (`t`) to get the player's team.
   * - `team_player` (`am`) to include other approved members of the team.
   * - `player_last` (`al`) to ensure at least one approved member has a VPN local address.
   *
   * @return TargetInstanceQuery
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

  /**
   * Filters target instances that are active.
   *
   * Active instances:
   * - Have a non-null IP address.
   * - Are not marked for destruction (`reboot != 2`).
   *
   * @return TargetInstanceQuery
   */
  public function active()
  {
    return $this->andWhere('target_instance.[[ip]] IS NOT NULL')->andWhere('target_instance.[[reboot]]!=2');
  }

  /**
   * Filters target instances last updated more than the given number of seconds ago.
   *
   * @param int $seconds_ago Minimum age in seconds since last update.
   * @return TargetInstanceQuery
   */
  public function last_updated(int $seconds_ago = 1)
  {
    return $this->andWhere(['<', 'target_instance.[[updated_at]]', new \yii\db\Expression("NOW() - INTERVAL $seconds_ago SECOND")]);
  }

  /**
   * Filters target instances by assigned server.
   *
   * - If `$server_id` is 0, matches instances assigned to any server.
   * - Otherwise, matches instances assigned to the specified server.
   *
   * @param int $server_id Server ID to filter by, or 0 for any assigned server.
   * @return TargetInstanceQuery
   */
  public function server_assigned(int $server_id = 0)
  {
    if ($server_id === 0)
      return $this->andWhere(['IS NOT', 'target_instance.server_id', null]);
    return $this->andWhere(['target_instance.server_id' => $server_id]);
  }

  /**
   * Selects target instances that require pending actions.
   *
   * Pending action criteria:
   * - IP address is null.
   * - `reboot` is greater than 0.
   * - Last updated more than `$seconds_ago` seconds ago.
   *
   * Overrides `reboot` to 2 (destroy) if the instance hasn't been updated within `$seconds_ago` seconds.
   *
   * @param int $seconds_ago Threshold in seconds to consider an instance outdated.
   * @return TargetInstanceQuery
   */
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
