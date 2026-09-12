-- repro_memc_churn.sql
--
-- Reproduces the Section 6.1 connection-churn mechanism directly: calls the
-- memcached_functions_mysql UDF (memc_get) the same way PlayerAR::find()'s
-- old query did, in a tight loop, so you can watch TIME_WAIT on the
-- memcached port and "kernel map entries" climb (and, if pushed hard
-- enough, reproduce uvm_mapent_alloc itself on demand).
--
-- WARNING: this deliberately reproduces the resource-exhaustion bug. Do
-- NOT run this against db.hackmex.mx while it's carrying real traffic.
-- Use an isolated test box with the same UDF installed, or an explicit
-- maintenance window with zero live players connected.
--
-- Usage:
--   CALL repro_memc_churn(1000, 42);
--   -- runs 1000 iterations of the memc_get() triple-call for player id 42
--
-- To simulate the real incident's concurrency (multiple VPN servers
-- firing this at once, not just one loop), CALL this same procedure from
-- several separate client sessions at the same time, rather than raising
-- the iteration count on a single call.
--
-- While it runs, watch from a shell on the same box:
--   watch -n1 "netstat -an | grep '\.11211' | grep TIME_WAIT | wc -l"
--   vmstat -s | grep 'kernel map entries'
--
-- After it stops, TIME_WAIT should drain back toward baseline over
-- roughly 2*MSL if this is genuine churn and not a leak.

DELIMITER $$

DROP PROCEDURE IF EXISTS repro_memc_churn$$

CREATE PROCEDURE repro_memc_churn(
    IN p_iterations INT,
    IN p_player_id INT
)
proc_body: BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_ovpn VARCHAR(255);
    DECLARE v_online VARCHAR(255);
    DECLARE v_last_seen VARCHAR(255);
    DECLARE v_start DATETIME(6);
    DECLARE v_end DATETIME(6);

    IF p_iterations IS NULL OR p_iterations <= 0 THEN
        SELECT 'p_iterations must be a positive integer' AS error;
        LEAVE proc_body;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM player WHERE id = p_player_id) THEN
        SELECT CONCAT('No player found with id ', p_player_id) AS error;
        LEAVE proc_body;
    END IF;

    SET v_start = NOW(6);

    WHILE i < p_iterations DO
        SELECT
            ifnull(memc_get(concat('ovpn:', p_player_id)), 0),
            ifnull(memc_get(concat('online:', p_player_id)), 0),
            memc_get(concat('last_seen:', p_player_id))
        INTO v_ovpn, v_online, v_last_seen;

        SET i = i + 1;
    END WHILE;

    SET v_end = NOW(6);

    SELECT
        p_iterations                                   AS iterations_run,
        p_player_id                                     AS player_id_tested,
        v_start                                          AS started_at,
        v_end                                            AS finished_at,
        TIMESTAMPDIFF(MICROSECOND, v_start, v_end) / 1000000.0 AS elapsed_seconds,
        p_iterations * 3                                 AS memcached_calls_made;

END$$

DELIMITER ;
