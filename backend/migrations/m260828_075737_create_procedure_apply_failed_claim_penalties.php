<?php

use yii\db\Migration;

class m260828_075737_create_procedure_apply_failed_claim_penalties extends Migration
{

  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%apply_failed_claim_penalties}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%apply_failed_claim_penalties}}()
BEGIN
  DECLARE free_fail_allowance INT DEFAULT 0;
  DECLARE penalty_per_fail INT DEFAULT 0;

  SET @row_number = 0;
  SET free_fail_allowance = memc_get('sysconfig:free_fail_allowance');
  SET penalty_per_fail   = memc_get('sysconfig:penalty_per_fail');

  CREATE TEMPORARY TABLE tmp_penalties ENGINE=MEMORY AS
  SELECT
      t.owner_id,
      tp.team_id,
      COUNT(DISTINCT tp.player_id) AS player_count,
      COALESCE(SUM(pcn.counter), 0) AS total_failed_claims,
      GREATEST(0, (COALESCE(SUM(pcn.counter), 0) - free_fail_allowance * COUNT(DISTINCT tp.player_id))) * penalty_per_fail AS penalty
  FROM team_player tp
  JOIN team t ON t.id = tp.team_id
  LEFT JOIN player_counter_nf pcn
      ON pcn.player_id = tp.player_id
      AND pcn.metric = 'failed_claims'
  WHERE tp.approved = 1
  GROUP BY tp.team_id, t.owner_id
  HAVING total_failed_claims >= free_fail_allowance * player_count;


  INSERT INTO stream (player_id, model, model_id, points, title, message, pubtitle, pubmessage, ts)
  SELECT
      p.owner_id,
      'abuse',
      @row_number := @row_number + 1 AS model_id,
      -1 * p.penalty,
      CONCAT('Got penalized for ', p.total_failed_claims, ' failed claims'),
      CONCAT('Got penalized for ', p.total_failed_claims, ' failed claims'),
      CONCAT('Got penalized for ', p.total_failed_claims, ' failed claims'),
      CONCAT('Got penalized for ', p.total_failed_claims, ' failed claims'),
      NOW()
  FROM tmp_penalties p;

  DROP TEMPORARY TABLE IF EXISTS tmp_penalties;
END";


  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}