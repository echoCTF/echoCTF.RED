--
-- Dumping routines for database 'echoCTF_dev'
--
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

DELIMITER ;;

DROP FUNCTION IF EXISTS `NTOHS` ;;
CREATE FUNCTION `NTOHS`(n SMALLINT UNSIGNED) RETURNS smallint(5) unsigned DETERMINISTIC
return ((((n & 0xFF)) << 8) | ((n & 0xFF00) >> 8)) ;;

DROP FUNCTION IF EXISTS `SPLIT_STR` ;;
CREATE FUNCTION `SPLIT_STR`( x VARCHAR(255), delim VARCHAR(12),  pos INT) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '') ;;


DROP FUNCTION IF EXISTS `TS_AGO` ;;
CREATE FUNCTION `TS_AGO`( x TIMESTAMP) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
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
CREATE PROCEDURE `add_finding_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE pts,ltid BIGINT;
  SELECT name,pubname,description,pubdescription,points,target_id INTO ltitle,lpubtitle,lmessage,lpubmessage,pts,ltid FROM finding WHERE id=recid;
  IF (SELECT count(*) FROM player_target_help WHERE target_id=ltid AND player_id=usid)>0 THEN
    SET pts=pts/2;
  END IF;
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
  WHEN 'badge' THEN CALL add_badge_stream(usid,tbl,recid);
  WHEN 'treasure' THEN CALL add_treasure_stream(usid,tbl,recid);
  WHEN 'finding' THEN CALL add_finding_stream(usid,tbl,recid);
  WHEN 'report' THEN CALL add_report_stream(usid,tbl,recid);
  WHEN 'question' THEN CALL add_question_stream(usid,tbl,recid);
  END CASE;
END ;;

DROP PROCEDURE IF EXISTS `add_treasure_stream` ;;
CREATE PROCEDURE `add_treasure_stream`(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE divider INTEGER DEFAULT 1;
  DECLARE pts,ltid BIGINT;
  SELECT name,pubname,description,pubdescription,points,target_id INTO ltitle,lpubtitle,lmessage,lpubmessage,pts,ltid FROM treasure WHERE id=recid;
  IF (SELECT count(*) FROM player_target_help WHERE target_id=ltid AND player_id=usid)>0 THEN
    SET pts=pts/2;
  END IF;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'treasure',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
END ;;

DROP PROCEDURE IF EXISTS `calculate_ranks` ;;
CREATE PROCEDURE `calculate_ranks`()
BEGIN
CREATE TEMPORARY TABLE `ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY;
START TRANSACTION;
  delete from player_rank;
  insert into ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 order by points desc,t.ts asc, t.player_id asc;
  insert into player_rank select * from ranking;
COMMIT;
DROP TABLE `ranking`;
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


DROP PROCEDURE IF EXISTS `time_headshot` ;;
CREATE PROCEDURE `time_headshot` (IN pid INT, IN tid INT)
BEGIN
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;
  SELECT min(ts),max(ts) INTO min_finding,max_finding FROM `player_finding` WHERE `player_id`=pid AND `finding_id` IN (SELECT `id` FROM `finding` WHERE `target_id`=tid);
  SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM `player_treasure` WHERE `player_id`=pid AND `treasure_id` IN (SELECT `id` FROM `treasure` WHERE `target_id`=tid);
  SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
  UPDATE `headshot` SET `timer`=UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val) WHERE `player_id`=pid AND `target_id`=tid;
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

DROP PROCEDURE IF EXISTS calculate_country_rank;;
CREATE PROCEDURE calculate_country_rank ()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE ccode VARCHAR(3) COLLATE utf8mb4_unicode_ci;
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
        WHERE t2.active=1 AND t3.country=ccode ORDER BY points DESC,t.ts ASC, t.player_id ASC;
      insert into player_country_rank select *,ccode from country_ranking ON DUPLICATE KEY UPDATE id=values(id),country=values(country);
    COMMIT;
    TRUNCATE country_ranking;
  END LOOP;
  CLOSE cur1;
  DROP TABLE country_ranking;
END ;;


DROP PROCEDURE IF EXISTS populate_memcache;;
CREATE PROCEDURE populate_memcache ()
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
END ;;

DROP PROCEDURE IF EXISTS init_mysql;;
CREATE PROCEDURE init_mysql ()
BEGIN
    IF (SELECT val FROM sysconfig WHERE id='time_zone') IS NOT NULL THEN
      SET GLOBAL time_zone=(SELECT val FROM sysconfig WHERE id='time_zone');
    END IF;
    call populate_memcache();
    call calculate_ranks();
    call calculate_country_rank();
    call calculate_team_ranks();
END ;;
