SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DELIMITER ;;

--
-- Dumping routines for database 'echoCTF'
--
DROP FUNCTION IF EXISTS `NTOHS` ;;
CREATE FUNCTION `NTOHS`(n SMALLINT UNSIGNED) RETURNS smallint(5) unsigned
    DETERMINISTIC
return ((((n & 0xFF)) << 8) | ((n & 0xFF00) >> 8)) ;;

DROP FUNCTION IF EXISTS `SPLIT_STR` ;;
CREATE FUNCTION `SPLIT_STR`(x VARCHAR(255), delim VARCHAR(12),  pos INT) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '') ;;

DROP FUNCTION IF EXISTS `target_solved_percentage` ;;
CREATE FUNCTION `target_solved_percentage`(n INT UNSIGNED) RETURNS float
    READS SQL DATA
BEGIN
  DECLARE average_pct float;
  SET average_pct=(select ((select count(*) from headshot where target_id=n)*100)/count(distinct player_id) from stream where (model='finding' and model_id in (select id from finding where target_id=n)) or (model='treasure' and model_id in (select id from treasure where target_id=n)));
  RETURN average_pct;
  END ;;

DROP FUNCTION IF EXISTS `target_started_count` ;;
CREATE FUNCTION `target_started_count`(n INT UNSIGNED) RETURNS int(11)
    READS SQL DATA
BEGIN
DECLARE counter INT;
SET counter=(select count(distinct player_id) from stream where (model='finding' and model_id in (select id from finding where target_id=n)) or (model='treasure' and model_id in (select id from treasure where target_id=n)));
RETURN counter;
END ;;


DROP FUNCTION IF EXISTS `TS_AGO` ;;
CREATE FUNCTION `TS_AGO`(x TIMESTAMP) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
BEGIN
  DECLARE minutes,seconds,hours,months,days INTEGER;
  DECLARE msg CHAR(255);
  SET seconds=timestampdiff(SECOND,x,now())%60;
  set months=timestampdiff(MONTH,x,now());
  set days=timestampdiff(DAY,x,now());
  set minutes=timestampdiff(MINUTE,x,now())%60;
  set hours=timestampdiff(HOUR,x,now())%24;
  SET msg='';
  IF months>1 THEN
  RETURN CONCAT(months,' months ago');
  ELSEIF months>0 THEN
  RETURN CONCAT(months,' month ago');
  END IF;

  IF days>1 THEN
  RETURN CONCAT(days,' days ago');
  elseif days>0 then
  RETURN CONCAT(days,' day ago');
  END IF;

  IF hours>1 THEN
  RETURN CONCAT(hours,' hours ago');
  ELSEIF hours>0 THEN
  RETURN CONCAT(hours,' hour ago');
  END IF;

  IF minutes>1 THEN
  RETURN CONCAT(minutes, ' minutes ago');
  ELSEIF minutes>0 THEN
  RETURN CONCAT(minutes, ' minute ago');
  END IF;

  IF seconds>1 THEN
  RETURN CONCAT(seconds, ' seconds ago');
  END IF;
  RETURN CONCAT(' just now');
END ;;


DROP PROCEDURE IF EXISTS `add_badge_stream` ;;
CREATE PROCEDURE `add_badge_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE pts BIGINT;
  SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM badge WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'badge',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
END ;;

DROP PROCEDURE IF EXISTS `add_finding_stream` ;;
CREATE PROCEDURE `add_finding_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT, IN pts FLOAT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE ltid BIGINT;
  SELECT name,pubname,description,pubdescription,target_id INTO ltitle,lpubtitle,lmessage,lpubmessage,ltid FROM finding WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'finding',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
END ;;

DROP PROCEDURE IF EXISTS `add_player_finding_hint` ;;
CREATE PROCEDURE `add_player_finding_hint`(p_id INT,f_id INT)
BEGIN
  INSERT INTO player_hint(player_id,hint_id)
  SELECT p_id, id
  FROM hint
  WHERE
    id NOT IN (SELECT hint_id FROM player_hint WHERE player_id = p_id)
     AND finding_id=f_id
     AND badge_id IS NULL
     AND treasure_id IS NULL
     AND `active`=1;
END ;;


DROP PROCEDURE IF EXISTS `add_player_treasure_hint` ;;
CREATE PROCEDURE `add_player_treasure_hint`(p_id INT,f_id INT)
BEGIN
  INSERT INTO player_hint(player_id,hint_id)
  SELECT p_id, id	FROM hint
  WHERE id NOT IN (SELECT hint_id FROM player_hint WHERE player_id = p_id)
  AND treasure_id=f_id
  AND badge_id IS NULL
  AND finding_id IS NULL
  AND `active`=1;
END ;;


DROP PROCEDURE IF EXISTS `add_question_stream` ;;
CREATE PROCEDURE `add_question_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE lname VARCHAR(255);
  DECLARE ldescription TEXT;
  DECLARE divider INTEGER DEFAULT 1;
  DECLARE pts BIGINT;
  SELECT name,description,points INTO lname,ldescription,pts FROM question WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'question',recid,pts,lname,ldescription,lname,ldescription,now());
END ;;


DROP PROCEDURE IF EXISTS `add_report_stream` ;;
CREATE PROCEDURE `add_report_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE `ltitle`, `lstatus`,`lpubtitle` VARCHAR(255);
  DECLARE lbody,lmodcomment,lpubbody TEXT;
  DECLARE pts BIGINT;
  select  `title`, `body`, `status`, `points`, `modcomment`, `pubtitle`, `pubbody` INTO `ltitle`, `lbody`, `lstatus`, `pts`, `lmodcomment`, `lpubtitle`, `lpubbody`  FROM report WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'report',recid,pts,ltitle,lbody,lpubtitle,lpubbody,now());
END ;;


DROP PROCEDURE IF EXISTS `add_stream` ;;
CREATE PROCEDURE `add_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  CASE tbl
--  WHEN 'user' THEN CALL add_user_stream(usid,tbl,recid);
  WHEN 'finding' THEN CALL add_finding_stream(usid,tbl,recid);
  WHEN 'treasure' THEN CALL add_treasure_stream(usid,tbl,recid);
  WHEN 'question' THEN CALL add_question_stream(usid,tbl,recid);
  WHEN 'badge' THEN CALL add_badge_stream(usid,tbl,recid);
--  WHEN 'headshot' THEN CALL add_headshot_stream(usid,tbl,recid);
--  WHEN 'challenge' THEN CALL add_challenge_stream(usid,tbl,recid);
  WHEN 'report' THEN CALL add_report_stream(usid,tbl,recid);
  END CASE;
END ;;

DROP PROCEDURE IF EXISTS `add_treasure_stream` ;;
CREATE PROCEDURE `add_treasure_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT, IN pts FLOAT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE divider INTEGER DEFAULT 1;
  DECLARE ltid BIGINT;

  SELECT name,pubname,description,pubdescription,target_id INTO ltitle,lpubtitle,lmessage,lpubmessage,ltid FROM treasure WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'treasure',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
END ;;

DROP PROCEDURE IF EXISTS `calculate_country_rank` ;;
CREATE PROCEDURE `calculate_country_rank`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE ccode VARCHAR(3);
    DECLARE cur1 CURSOR FOR SELECT DISTINCT country FROM profile;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    CREATE TEMPORARY TABLE country_ranking (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY;
    OPEN cur1;
    read_loop: LOOP
      FETCH cur1 INTO ccode;
      IF done THEN
        LEAVE read_loop;
      END IF;
      START TRANSACTION;
        delete from player_country_rank WHERE country=ccode;
        insert into country_ranking SELECT NULL,t.player_id FROM player_score AS t
          LEFT JOIN player AS t2 ON t.player_id=t2.id
          LEFT JOIN profile AS t3 ON t.player_id=t3.player_id
          WHERE t2.active=1 and t2.status=10 AND t3.country=ccode ORDER BY points DESC,t.ts ASC, t.player_id ASC;
        insert into player_country_rank select *,ccode from country_ranking ON DUPLICATE KEY UPDATE id=values(id),country=values(country);
      COMMIT;
      TRUNCATE country_ranking;
    END LOOP;
    CLOSE cur1;
    DROP TABLE country_ranking;
END ;;

DROP PROCEDURE IF EXISTS `calculate_ranks` ;;
CREATE PROCEDURE `calculate_ranks`()
BEGIN
  DECLARE v_max INT unsigned DEFAULT 0;
  DECLARE v_counter INT unsigned DEFAULT 0;
  SET v_max=(SELECT IFNULL(memc_get('sysconfig:academic_grouping'),0));

  DROP TABLE IF EXISTS pr_ranking;

  IF v_max = 0 THEN
    CREATE TEMPORARY TABLE `pr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
    delete from player_rank;
    insert into pr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 order by points desc,t.ts asc, t.player_id asc;
    insert IGNORE into player_rank select * from pr_ranking;
    COMMIT;
    DROP TABLE `pr_ranking`;
  ELSE
    REPEAT
      CREATE TEMPORARY TABLE `pr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
      START TRANSACTION;
        delete from player_rank where player_id in (select id from player where academic=v_counter) OR player_id NOT IN (select id from player);
        insert into pr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 and t2.academic=v_counter order by points desc,t.ts asc, t.player_id asc;
        insert IGNORE into player_rank select * from pr_ranking;
      COMMIT;
      DROP TABLE `pr_ranking`;
      SET v_counter=v_counter+1;
      UNTIL  v_counter >= v_max
    END REPEAT;
  END IF;
END ;;

DROP PROCEDURE IF EXISTS `calculate_team_ranks` ;;
CREATE PROCEDURE `calculate_team_ranks`()
BEGIN
  DECLARE v_max INT unsigned DEFAULT 0;
  DECLARE v_counter INT unsigned DEFAULT 0;

  SET v_max=(SELECT IFNULL(memc_get('sysconfig:academic_grouping'),0));

  DROP TABLE IF EXISTS `tr_ranking`;

  IF v_max = 0 THEN
    CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
    delete from team_rank;
    insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id ORDER BY points desc,t.ts asc, t.team_id asc;
    insert IGNORE into team_rank select * from tr_ranking;
    COMMIT;
    DROP TABLE `tr_ranking`;
  ELSE
    IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
      REPEAT
        CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        START TRANSACTION;
          -- DELETE team of a given academic category or no longer exist in the table
          delete from team_rank where team_id in (select id from team where academic=v_counter) OR team_id NOT IN (select id from team);
          insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id WHERE t2.academic=v_counter order by points desc,t.ts asc, t.team_id asc;
          insert IGNORE into team_rank select * from tr_ranking;
        COMMIT;
        DROP TABLE `tr_ranking`;
        SET v_counter=v_counter+1;
        UNTIL  v_counter >= v_max
      END REPEAT;
    END IF;
  END IF;
END ;;

DROP PROCEDURE IF EXISTS `expire_player_tokens` ;;
CREATE PROCEDURE `expire_player_tokens`()
BEGIN
  DECLARE tnow TIMESTAMP;
  SET tnow=NOW();
  IF (SELECT COUNT(*) FROM player_token WHERE expires_at<tnow and `type`='API')>0 THEN
    START TRANSACTION;
    INSERT INTO notification (player_id,category,title,body,archived,created_at,updated_at) SELECT player_id,'info','Token expiration',CONCAT(type,' Token [',description,'] expired at ',expires_at),0,tnow,tnow FROM player_token WHERE expires_at<tnow and `type`='API';
    DELETE FROM player_token WHERE expires_at<tnow and `type`='API';
    COMMIT;
  END IF;
  IF (SELECT COUNT(*) FROM player_token WHERE expires_at<tnow)>0 THEN
    DELETE FROM player_token WHERE expires_at<tnow;
  END IF;
END ;;

DROP PROCEDURE IF EXISTS `give_all_challenge_solver` ;;
CREATE PROCEDURE `give_all_challenge_solver`()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE lpid,lcid,local_challenge_id INT DEFAULT 0;
  DECLARE cur1 CURSOR FOR SELECT t.player_id,t2.challenge_id FROM player_question t LEFT JOIN question AS t2 ON t2.id=t.question_id GROUP BY t.player_id,t2.challenge_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN cur1;
  read_loop: LOOP
    FETCH cur1 INTO lpid,lcid;
    IF done THEN
      LEAVE read_loop;
    END IF;
    CALL give_challenge_solver(lpid,lcid,0);
  END LOOP;

  CLOSE cur1;
END ;;

DROP PROCEDURE IF EXISTS `give_challenge_solver` ;;
CREATE PROCEDURE `give_challenge_solver`(IN pid INT, IN tid INT, IN ttimer INT)
BEGIN
  DECLARE completed INT default null;
  DECLARE min_question,max_question, max_val, min_val DATETIME default null;

  SET completed=(select true as completed FROM challenge as t left join question as t2 on t2.challenge_id=t.id LEFT JOIN player_question as t4 on t4.question_id=t2.id and t4.player_id=pid WHERE t.id=tid GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.question_id));
  IF completed IS NOT NULL and completed=true THEN
    SELECT min(ts),max(ts) INTO min_question,max_question FROM player_question WHERE player_id=pid AND question_id IN (SELECT id FROM question WHERE challenge_id=tid);
    SELECT GREATEST(max_question, min_question), LEAST(min_question, max_question) INTO max_val,min_val;
    INSERT IGNORE INTO challenge_solver (player_id,challenge_id,created_at,timer) VALUES (pid,tid,max_val,UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
    IF ROW_COUNT()>0 THEN
      INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (pid,'challenge',tid,0,'','','','',max_val);
    END IF;
  END IF;

END ;;


DROP PROCEDURE IF EXISTS `give_headshot` ;;
CREATE PROCEDURE `give_headshot`(IN usid BIGINT, IN tid INT, IN ttimer INT)
BEGIN
  INSERT IGNORE INTO player_finding (player_id,finding_id) SELECT usid,id FROM finding WHERE target_id=tid ORDER BY id DESC;
  INSERT IGNORE INTO player_treasure (player_id,treasure_id) SELECT usid,id FROM treasure WHERE target_id=tid ORDER BY id DESC;
  IF ttimer>0 THEN
    UPDATE headshot SET timer=ttimer WHERE player_id=usid AND target_id=tid;
  END IF;
END ;;

DROP PROCEDURE IF EXISTS `init_mysql` ;;
CREATE PROCEDURE `init_mysql`()
BEGIN
  IF (SELECT val FROM sysconfig WHERE id='time_zone') IS NOT NULL THEN
    SET GLOBAL time_zone=(SELECT val FROM sysconfig WHERE id='time_zone');
  END IF;
  call populate_memcache();
  call calculate_ranks();
  call calculate_country_rank();
  call calculate_team_ranks();
END ;;

DROP PROCEDURE IF EXISTS `player_maintenance` ;;
CREATE PROCEDURE `player_maintenance`()
BEGIN
  DECLARE player_require_approval,player_delete_inactive_after,player_delete_deleted_after,player_changed_to_deleted_after,player_delete_rejected_after INT;
  SET player_require_approval=memc_get('sysconfig:player_require_approval');
  SET player_delete_inactive_after=memc_get('sysconfig:player_delete_inactive_after');
  SET player_delete_deleted_after=memc_get('sysconfig:player_delete_deleted_after');
  SET player_changed_to_deleted_after=memc_get('sysconfig:player_changed_to_deleted_after');
  SET player_delete_rejected_after=memc_get('sysconfig:player_delete_rejected_after');

  IF player_require_approval IS NOT NULL and player_require_approval>0 AND player_delete_rejected_after IS NOT NULL AND player_delete_rejected_after>0 THEN
    -- DELETE players who have been rejected after 5 days
    SELECT 'player_require_approval' as '';
    DELETE FROM `player` WHERE `ts` < NOW() - INTERVAL player_delete_rejected_after DAY AND `status`=9 AND approval=4;
  END IF;
  IF player_delete_inactive_after IS NOT NULL AND player_delete_inactive_after > 0 THEN
    SELECT 'player_delete_inactive_after' as '';
    DELETE FROM `player` WHERE `ts` < NOW() - INTERVAL player_delete_inactive_after DAY AND `status`=9;
  END IF;
  IF player_delete_deleted_after IS NOT NULL AND player_delete_deleted_after > 0 THEN
    SELECT 'player_delete_deleted_after' as '';
    DELETE FROM `player` WHERE `ts` < NOW() - INTERVAL player_delete_deleted_after DAY AND `status`=0;
  END IF;
  IF player_changed_to_deleted_after IS NOT NULL AND player_changed_to_deleted_after > 0 THEN
    UPDATE player SET status=0 WHERE status=8 AND ts < NOW() - INTERVAL player_changed_to_deleted_after DAY;
  END IF;
END ;;

DROP PROCEDURE IF EXISTS `populate_memcache` ;;
CREATE PROCEDURE `populate_memcache`()
BEGIN
  select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  INSERT INTO devnull SELECT memc_set(CONCAT('player:',id),id) FROM player;
  INSERT INTO devnull SELECT memc_set(CONCAT('player_type:',id),`type`) FROM player;
  INSERT INTO devnull SELECT memc_set(CONCAT('team_player:',player_id),team_id) FROM team_player;
  INSERT INTO devnull SELECT memc_set(CONCAT('team_finding:',t2.team_id, ':', t1.finding_id),t1.player_id) FROM player_finding AS t1 LEFT JOIN team_player AS t2 ON t2.player_id=t1.player_id;
  INSERT INTO devnull SELECT memc_set(CONCAT('player_finding:',player_id, ':', finding_id),player_id) FROM player_finding;
  INSERT INTO devnull SELECT memc_set(CONCAT('target:',ip),id) FROM target;
  INSERT INTO devnull SELECT memc_set(CONCAT('target:',id),ip) FROM target;
  INSERT INTO devnull SELECT memc_set(CONCAT('sysconfig:',id),val) FROM sysconfig;
  INSERT INTO devnull SELECT memc_set(CONCAT('finding:',protocol,':',ifnull(port,0), ':', target_id ),id) FROM finding;
  DO memc_set('sysconfig_json',(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('id', id,'val',val) ORDER BY id),']') FROM sysconfig WHERE id NOT LIKE 'CA%' and id NOT IN ('disabled_routes','frontpage_scenario','routes','writeup_rules','vpn-ta.key') ORDER BY id));
END ;;

DROP PROCEDURE IF EXISTS `repopulate_team_stream` ;;
CREATE PROCEDURE `repopulate_team_stream`(IN tid INT)
BEGIN
  DECLARE `_rollback` BOOL DEFAULT false;
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = true;
  IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
    START TRANSACTION;
    UPDATE team_score SET points=0 WHERE team_id=tid;
    DELETE FROM team_stream WHERE team_id=tid;
    INSERT INTO team_stream SELECT tid,model,model_id,points,ts FROM stream WHERE model!='user' AND player_id IN (select player_id FROM team_player WHERE team_id=tid) GROUP BY model,model_id ORDER BY id,ts;
    IF `_rollback` THEN
        ROLLBACK;
    ELSE
        COMMIT;
    END IF;
  END IF;
END ;;


DROP PROCEDURE IF EXISTS `reset_gamedata` ;;
CREATE PROCEDURE `reset_gamedata`()
BEGIN
  DELETE FROM `badge`;
  ALTER TABLE `badge` AUTO_INCREMENT=1;

  DELETE FROM `finding`;
  ALTER TABLE `finding` AUTO_INCREMENT=1;

  DELETE FROM `treasure`;
  ALTER TABLE `treasure` AUTO_INCREMENT=1;

  DELETE FROM `hint` where id>1;
  ALTER TABLE `hint` AUTO_INCREMENT=1;

  CALL reset_playdata();

  DELETE FROM `target`;
  ALTER TABLE `target` AUTO_INCREMENT=1;

END ;;


DROP PROCEDURE IF EXISTS `reset_playdata` ;;
CREATE PROCEDURE `reset_playdata`()
BEGIN
  DELETE FROM `player`;
  ALTER TABLE `player` AUTO_INCREMENT=1;

  DELETE FROM `team`;
  ALTER TABLE `team` AUTO_INCREMENT=1;

  TRUNCATE report;
  TRUNCATE team_score;
  TRUNCATE player_score;
  TRUNCATE sessions;
END ;;


DROP PROCEDURE IF EXISTS `reset_player_progress` ;;
CREATE PROCEDURE `reset_player_progress`()
BEGIN
  TRUNCATE report;
  TRUNCATE team_score;
  TRUNCATE player_score;
  TRUNCATE player_finding;
  TRUNCATE player_treasure;
  TRUNCATE player_question;
  TRUNCATE player_hint;
  TRUNCATE player_badge;
  TRUNCATE sessions;
  TRUNCATE stream;
  insert into team_score (team_id,points) select id,0 from team;
  insert into player_score (player_id,points) select id,0 from player;
END ;;

DROP PROCEDURE IF EXISTS `rotate_notifications` ;;
CREATE PROCEDURE `rotate_notifications`(IN archived_interval_minute INT, IN pending_interval_minute INT)
BEGIN
  DELETE FROM `notification` WHERE
    (`archived`=1 AND DATE(`updated_at`) < NOW() - INTERVAL archived_interval_minute MINUTE) OR
    (created_at IS null AND updated_at IS null) OR
    (title LIKE '%target%' and DATE(created_at) < NOW() - INTERVAL pending_interval_minute MINUTE);
END ;;


DROP PROCEDURE IF EXISTS `time_headshot` ;;
CREATE PROCEDURE `time_headshot`(IN pid INT, IN tid INT)
BEGIN
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;
  SELECT min(ts),max(ts) INTO min_finding,max_finding FROM `player_finding` WHERE `player_id`=pid AND `finding_id` IN (SELECT `id` FROM `finding` WHERE `target_id`=tid);
  SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM `player_treasure` WHERE `player_id`=pid AND `treasure_id` IN (SELECT `id` FROM `treasure` WHERE `target_id`=tid);
  SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
  UPDATE `headshot` SET `timer`=UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val) WHERE `player_id`=pid AND `target_id`=tid;
END ;;

DROP PROCEDURE IF EXISTS `time_headshot_with_player` ;;
CREATE PROCEDURE `time_headshot_with_player`(IN pid INT, IN tid INT, IN aspid INT)
BEGIN
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;
  SELECT min(ts),max(ts) INTO min_finding,max_finding FROM player_finding WHERE player_id IN (pid, aspid) AND finding_id IN (SELECT id FROM finding WHERE target_id=tid);
  SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM player_treasure WHERE player_id IN (pid,aspid) AND treasure_id IN (SELECT id FROM treasure WHERE target_id=tid);
  SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
  UPDATE `headshot` SET timer=UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val) WHERE player_id=pid AND target_id=tid;
END ;;

DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
