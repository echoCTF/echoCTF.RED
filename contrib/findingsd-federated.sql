/* $Id$ */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8 COLLATE 'utf8_unicode_ci';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `findingsd`;
CREATE TABLE `findingsd` (
  srchw CHAR(17) NOT NULL DEFAULT 'ff',
  dsthw CHAR(17) NOT NULL DEFAULT 'ff',
  `size` INT NOT NULL DEFAULT 0,
  proto ENUM('tcp','udp','icmp','tell') default 'tcp',
  srcip BIGINT UNSIGNED default 0,
  dstip BIGINT UNSIGNED default 0,
  dstport INT DEFAULT 0
) ENGINE=BLACKHOLE DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS player_finding;
CREATE TABLE player_finding (
  `player_id` int UNSIGNED NOT NULL,
  `finding_id` int(11) NOT NULL,
  `ts` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (player_id,finding_id)
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/player_finding';

DROP TABLE IF EXISTS `player_last`;
CREATE TABLE `player_last` (
  `id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `on_pui` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `on_vpn` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vpn_remote_address` int(11) UNSIGNED DEFAULT NULL,
  `vpn_local_address` int(11) UNSIGNED DEFAULT NULL
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/player_last';

DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) DEFAULT '0'
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/player';

DROP TABLE IF EXISTS `network`;
CREATE TABLE `network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `public` tinyint(1) DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  `codename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/network';


DROP TABLE IF EXISTS `network_player`;
CREATE TABLE `network_player` (
  `network_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`network_id`,`player_id`),
  KEY `idx-network_player-network_id` (`network_id`),
  KEY `idx-network_player-player_id` (`player_id`)
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/network_player';

DROP TABLE IF EXISTS `target_ondemand`;
CREATE TABLE `target_ondemand` (
  `target_id` int(11) NOT NULL,
  `player_id` int(11) unsigned DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '-1',
  `heartbeat` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=FEDERATED DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CONNECTION='mysql://{{db_user}}:{{db_pass}}@{{db_host}}:3306/{{db_name}}/target_ondemand';

DROP TABLE IF EXISTS `debuglogs`;
CREATE TABLE debuglogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  msg longtext
) engine=myisam;

DELIMITER //
DROP TRIGGER IF EXISTS tbi_tcpdump_bh//
CREATE TRIGGER tbi_tcpdump_bh BEFORE INSERT ON findingsd FOR EACH ROW
BEGIN
  DECLARE PLAYER_ID, FINDING_ID, TARGET_ID, TEAM_ID, CLAIMED_BEFORE, mac_auth, trust_user_ip,teams INT(11) DEFAULT NULL;
  DECLARE userMAC VARCHAR(32) DEFAULT NULL;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('{{db_host}}') INTO @memc_server_set_status;
  END IF;
  SELECT memc_get('sysconfig:debug') INTO @debug;
  SELECT memc_get('sysconfig:mac_auth') INTO mac_auth;
  SELECT memc_get('sysconfig:teams') INTO teams;

  SELECT memc_get(CONCAT('target:',NEW.dstip)) INTO TARGET_ID;
  IF TARGET_ID IS NOT NULL THEN
    SELECT memc_get(CONCAT('finding:',NEW.proto,':',ifnull(NEW.dstport,0), ':', TARGET_ID )) INTO FINDING_ID;
  END IF;


  IF mac_auth IS NOT NULL AND mac_auth=1 THEN
    SELECT memc_get(CONCAT('arpdat:',NEW.srcip)) INTO userMAC;
    SELECT memc_get(CONCAT('player_mac:',userMAC)) INTO PLAYER_ID;
  ELSE
    SELECT memc_get(CONCAT('ovpn:',INET_NTOA(NEW.srcip))) INTO PLAYER_ID;
  END IF;

  IF teams IS NOT NULL AND teams=1 THEN
    SELECT memc_get(CONCAT('team_player:',PLAYER_ID)) INTO TEAM_ID;
    SELECT memc_get(CONCAT('team_finding:',TEAM_ID, ':', FINDING_ID)) INTO CLAIMED_BEFORE;
  ELSE
    SELECT memc_get(CONCAT('player_finding:',PLAYER_ID, ':', FINDING_ID)) INTO CLAIMED_BEFORE;
  END IF;
  IF @debug IS NOT NULL AND @debug=1 THEN
    INSERT DELAYED into debuglogs (msg) VALUES (CONCAT('[BEFORE FINDING] TARGET_ID:',ifnull(TARGET_ID,0),' PLAYER_ID:',ifnull(PLAYER_ID,0),' FINDING_ID:',ifnull(FINDING_ID,0),' TEAM_ID:',ifnull(TEAM_ID,'-'),' CLAIMED BEFORE ID:', ifnull(CLAIMED_BEFORE,0)));
  END IF;
  IF PLAYER_ID IS NOT NULL AND FINDING_ID IS NOT NULL AND FINDING_ID>0 THEN
    UPDATE target_ondemand SET heartbeat=NOW() WHERE target_id=TARGET_ID AND state>0;
  END IF;
  IF PLAYER_ID IS NOT NULL AND FINDING_ID IS NOT NULL AND FINDING_ID>0 AND CLAIMED_BEFORE IS NULL THEN
    INSERT IGNORE INTO player_finding (finding_id,player_id) VALUES(FINDING_ID,PLAYER_ID) on duplicate key update finding_id=values(finding_id);
    IF @debug IS NOT NULL AND @debug=1 THEN
      INSERT DELAYED into debuglogs (msg) VALUES (CONCAT('[AFTER FINDING] TARGET_ID:',ifnull(TARGET_ID,0),' PLAYER_ID:',ifnull(PLAYER_ID,0),' FINDING_ID:',ifnull(FINDING_ID,0),' TEAM_ID:',ifnull(TEAM_ID,'-'),' CLAIMED BEFORE ID:', ifnull(CLAIMED_BEFORE,0)));
    END IF;
  END IF; /* END count target_mem */
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGIN` //
CREATE PROCEDURE `VPN_LOGIN`(IN usid BIGINT, IN  assignedIP INT UNSIGNED, IN remoteIP INT UNSIGNED)
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('{{db_host}}') INTO @memc_server_set_status;
  END IF;

  IF (SELECT COUNT(*) FROM player WHERE id=usid AND status=10)>0 THEN
    SELECT memc_set(CONCAT('ovpn:',usid),INET_NTOA(assignedIP)) into @devnull;
    SELECT memc_set(CONCAT('ovpn:',INET_NTOA(assignedIP)),usid) into @devnull;
    SELECT memc_set(CONCAT('ovpn_remote:',usid),INET_NTOA(remoteIP)) into @devnull;
    UPDATE `player_last` SET `on_vpn`=NOW(), `vpn_local_address`=assignedIP, `vpn_remote_address`=remoteIP WHERE `id`=usid;
  END IF;
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGOUT_STALLED` //
CREATE PROCEDURE `VPN_LOGOUT_STALLED`(IN vpn_server CHAR(15))
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('{{db_host}}') INTO @memc_server_set_status;
  END IF;
  SELECT memc_delete(CONCAT('ovpn:',id)), memc_delete(CONCAT('ovpn_remote:',id)) from player_last where vpn_remote_address is not null or vpn_local_address is not null;
  UPDATE player_last SET vpn_remote_address=null, vpn_local_address=null where vpn_remote_address is not null or vpn_local_address is not null;
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGOUT` //
CREATE PROCEDURE `VPN_LOGOUT`(IN usid BIGINT, IN  assignedIP INT UNSIGNED, IN remoteIP INT UNSIGNED)
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('{{db_host}}') INTO @memc_server_set_status;
  END IF;

  IF (SELECT COUNT(*) FROM player WHERE id=usid AND status=10)>0 THEN
    SELECT memc_delete(CONCAT('ovpn:',usid)) into @devnull;
    SELECT memc_delete(CONCAT('ovpn_remote:',usid)) into @devnull;
    SELECT memc_delete(CONCAT('ovpn:',INET_NTOA(assignedIP))) into @devnull;
    UPDATE `player_last` SET vpn_local_address=NULL, vpn_remote_address=NULL WHERE `id`=usid;
  END IF;
END
//
