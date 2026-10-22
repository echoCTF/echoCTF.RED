<?php

use yii\db\Migration;

class m261022_100000_create_procedure_and_event_scan_failed_claims extends Migration
{
  public $DROP_EVENT_SQL = "DROP EVENT IF EXISTS {{%ev_scan_failed_claims}}";
  public $DROP_SQL = "DROP PROCEDURE IF EXISTS {{%scan_failed_claims}}";
  public $CREATE_SQL = <<<'SQL'
CREATE PROCEDURE {{%scan_failed_claims}}()
scan: BEGIN
  DECLARE v_key VARCHAR(255);
  DECLARE v_title VARCHAR(255);
  DECLARE v_hash VARCHAR(255);
  DECLARE v_hit VARCHAR(64);
  DECLARE v_players INT;
  DECLARE v_after INT;
  DECLARE v_upto INT;
  DECLARE v_cnt INT;
  DECLARE v_owner INT;
  DECLARE v_pen INT;
  DECLARE v_apply TINYINT;
  DECLARE v_n INT;
  DECLARE v_i INT DEFAULT 0;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    DROP TEMPORARY TABLE IF EXISTS tmp_scan_titles;
    DROP TEMPORARY TABLE IF EXISTS tmp_settle;
    RESIGNAL;
  END;

  SET v_key = memc_get('sysconfig:treasure_secret_key');
  IF v_key IS NULL OR v_key = '' THEN
    LEAVE scan;
  END IF;
  SET v_players = IFNULL(NULLIF(memc_get('sysconfig:failed_claim_scan_players'), ''), 1000) + 0;
  IF v_players < 1 THEN
    SET v_players = 1000;
  END IF;
  SET v_apply = IFNULL(memc_get('sysconfig:apply_abuse_penalties'), '0') NOT IN ('', '0');

  DROP TEMPORARY TABLE IF EXISTS tmp_scan_titles;
  CREATE TEMPORARY TABLE tmp_scan_titles (n INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL);
  INSERT INTO tmp_scan_titles (title)
    SELECT DISTINCT title FROM abuser WHERE model = 'failed_claim' AND reason = 'failed_claim_pending' AND title IS NOT NULL;
  SELECT COUNT(*) INTO v_n FROM tmp_scan_titles;

  WHILE v_i < v_n DO
    SET v_i = v_i + 1;
    SELECT title INTO v_title FROM tmp_scan_titles WHERE n = v_i;

    SET v_hash = CASE
      WHEN v_title REGEXP '(?i)^[0-9a-f]{32}\\z' THEN v_title
      WHEN v_title REGEXP '(?i)^ETSCTF[_: .-][0-9a-f]{32}\\z' THEN SUBSTRING(v_title, 8)
      WHEN v_title REGEXP '(?i)^ETSCTF[{][0-9a-f]{32}[}]\\z' THEN SUBSTRING(v_title, 9, 32)
      ELSE NULL
    END;

    SET v_after = IFNULL(memc_get(CONCAT('failed_claim_scan:', MD5(v_title))), 0) + 0;
    SELECT MAX(s.id), COUNT(*) INTO v_upto, v_cnt FROM (SELECT id FROM player WHERE id > v_after ORDER BY id LIMIT v_players) s;

    SET v_hit = NULL;
    IF v_hash IS NOT NULL AND v_upto IS NOT NULL THEN
      SET v_hit = (
        SELECT CONCAT(p.id, ':', IFNULL(t.points, 0))
        FROM treasure t, player p
        WHERE t.appears != 0
          AND p.id > v_after AND p.id <= v_upto
          AND p.id NOT IN (SELECT player_id FROM abuser WHERE model = 'failed_claim' AND reason = 'failed_claim_pending' AND title = v_title)
          AND md5(HEX(AES_ENCRYPT(CONCAT(t.code, p.id), v_key))) = v_hash
        LIMIT 1);
    END IF;

    IF v_hit IS NOT NULL THEN
      SET v_owner = SUBSTRING_INDEX(v_hit, ':', 1) + 0;
      SET v_pen = SUBSTRING_INDEX(v_hit, ':', -1) + 0;
      SET v_pen = IF(v_pen = 0, -1500, -ABS(v_pen));

      START TRANSACTION;
      DROP TEMPORARY TABLE IF EXISTS tmp_settle;
      CREATE TEMPORARY TABLE tmp_settle (id INT NOT NULL PRIMARY KEY, player_id INT UNSIGNED NOT NULL);
      INSERT INTO tmp_settle
        SELECT id, player_id FROM abuser WHERE model = 'failed_claim' AND reason = 'failed_claim_pending' AND title = v_title FOR UPDATE;
      UPDATE abuser a JOIN tmp_settle s ON s.id = a.id
        SET a.reason = 'claim_other_team', a.points = IF(v_apply, v_pen, 0), a.resolved = v_apply, a.updated_at = NOW();
      IF v_apply THEN
        INSERT INTO stream (player_id, model, model_id, points, title, message, pubtitle, pubmessage)
          SELECT s.player_id, 'abuse', UNIX_TIMESTAMP(), v_pen,
            'Tried to claim a flag that belongs to another team', 'Tried to claim a flag that belongs to another team',
            'Tried to claim a flag that belongs to another team', 'Tried to claim a flag that belongs to another team'
          FROM tmp_settle s;
        INSERT INTO stream (player_id, model, model_id, points, title, message, pubtitle, pubmessage)
          SELECT v_owner, 'abuse', UNIX_TIMESTAMP(), v_pen,
            'Shared a flag with another team', 'Shared a flag with another team',
            'Shared a flag with another team', 'Shared a flag with another team'
          FROM tmp_settle s;
      END IF;
      INSERT INTO abuser (player_id, title, reason, model, model_id, points, resolved, created_at, updated_at)
        SELECT v_owner, v_title, 'flag_shared', 'failed_claim', s.id, IF(v_apply, v_pen, 0), v_apply, NOW(), NOW()
        FROM tmp_settle s;
      COMMIT;
      DROP TEMPORARY TABLE IF EXISTS tmp_settle;
      DO memc_set(CONCAT('failed_claim_scan:', MD5(v_title)), 0, 604800);
    ELSEIF v_hash IS NULL OR v_cnt < v_players THEN
      UPDATE abuser SET reason = 'failed_claim', updated_at = NOW()
        WHERE model = 'failed_claim' AND reason = 'failed_claim_pending' AND title = v_title;
      DO memc_set(CONCAT('failed_claim_scan:', MD5(v_title)), 0, 604800);
    ELSE
      DO memc_set(CONCAT('failed_claim_scan:', MD5(v_title)), v_upto, 604800);
    END IF;
  END WHILE;

  DROP TEMPORARY TABLE IF EXISTS tmp_scan_titles;
END
SQL;
  public $CREATE_EVENT_SQL = <<<'SQL'
CREATE EVENT {{%ev_scan_failed_claims}} ON SCHEDULE EVERY 1 MINUTE ON COMPLETION PRESERVE ENABLE DO
BEGIN
  IF GET_LOCK('ev_scan_failed_claims', 0) = 1 THEN
    CALL scan_failed_claims();
    DO RELEASE_LOCK('ev_scan_failed_claims');
  END IF;
END
SQL;

  public function up()
  {
    $this->db->createCommand($this->DROP_EVENT_SQL)->execute();
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
    $this->db->createCommand($this->CREATE_EVENT_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_EVENT_SQL)->execute();
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}
