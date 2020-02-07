--
-- Dumping routines for database 'echoCTF_dev'
--
DELIMITER ;;

DROP FUNCTION IF EXISTS `NTOHS` ;;
CREATE FUNCTION `NTOHS`(n SMALLINT UNSIGNED) RETURNS smallint(5) unsigned DETERMINISTIC
return ((((n & 0xFF)) << 8) | ((n & 0xFF00) >> 8)) ;;

DROP FUNCTION IF EXISTS `SPLIT_STR` ;;
CREATE FUNCTION `SPLIT_STR`( x VARCHAR(255), delim VARCHAR(12),  pos INT) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci
    DETERMINISTIC
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '') ;;

DROP FUNCTION IF EXISTS `TS_AGO` ;;
CREATE FUNCTION `TS_AGO`( x TIMESTAMP) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci
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
  DECLARE pts BIGINT;
  SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM finding WHERE id=recid;
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
  DECLARE pts BIGINT;
  SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM treasure WHERE id=recid;
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

	TRUNCATE finding_packet;
END ;;


DROP PROCEDURE IF EXISTS `reset_playdata` ;;
CREATE PROCEDURE `reset_playdata`()
BEGIN
	DELETE FROM `player`;
	ALTER TABLE `player` AUTO_INCREMENT=1;

	DELETE FROM `team`;
	ALTER TABLE `team` AUTO_INCREMENT=1;

	TRUNCATE player_mem;
	TRUNCATE player_mac;
	TRUNCATE player_mac_mem;
	TRUNCATE report;
	TRUNCATE arpdat;
	TRUNCATE tcpdump;
	TRUNCATE vtcpdump;
	TRUNCATE bridge_ruleset;
	TRUNCATE team_score;
	TRUNCATE player_score;
	TRUNCATE sessions;
	TRUNCATE sshkey;
	TRUNCATE finding_packet;
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
	TRUNCATE finding_packet;
	insert into team_score (team_id,points) select id,0 from team;
	insert into player_score (player_id,points) select id,0 from player;
END ;;
