<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetInstanceAudit]].
 *
 * @see TargetInstanceAudit
 */
class TargetInstanceAuditQuery extends \yii\db\ActiveQuery
{
  /**
   * Limit results to the latest row per (player_id, target_id),
   * based on highest id (requires MySQL 8+ or MariaDB 10.2+).
   */
  public function latestPerPlayerInstance()
  {
    $table = $this->modelClass::tableName();

    $subQuery = (new \yii\db\Query())
      ->select([
        "$table.*",
        new \yii\db\Expression(
          'ROW_NUMBER() OVER (PARTITION BY player_id, target_id ORDER BY id DESC) AS rn'
        ),
      ])
      ->from($table);

    return $this
      ->from(['ranked' => $subQuery])
      ->andWhere(['rn' => 1]);
  }

  /**
   * Get rows in the last X seconds.
   *
   * @param int $ago
   * @param string $unit
   * @return $this
   */
  public function since($ago = 60,$unit="SECOND")
  {
    return $this->andWhere(new \yii\db\Expression("ts > (NOW() - INTERVAL {$ago} {$unit})"));
  }

  /**
   * {@inheritdoc}
   * @return TargetInstanceAudit[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return TargetInstanceAudit|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
