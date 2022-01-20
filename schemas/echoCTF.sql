/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pubname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` text COLLATE utf8mb4_unicode_ci,
  `pubdescription` text COLLATE utf8mb4_unicode_ci COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offense',
  `appears` int(11) DEFAULT '0',
  `effects` enum('users_id','team','total') COLLATE utf8mb4_unicode_ci DEFAULT 'users_id',
  `code` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avatar`
--

DROP TABLE IF EXISTS `avatar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avatar` (
  `id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge`
--

DROP TABLE IF EXISTS `badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pubname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` text COLLATE utf8mb4_unicode_ci,
  `pubdescription` text COLLATE utf8mb4_unicode_ci COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Badges are given to the users based on gathering certain other achievements, treasures and findings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_finding`
--

DROP TABLE IF EXISTS `badge_finding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_finding` (
  `badge_id` int(11) NOT NULL DEFAULT '0',
  `finding_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`badge_id`,`finding_id`),
  KEY `finding_id` (`finding_id`),
  CONSTRAINT `badge_finding_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `badge_finding_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='What treasures are needed to get this badge';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_treasure`
--

DROP TABLE IF EXISTS `badge_treasure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_treasure` (
  `badge_id` int(11) NOT NULL DEFAULT '0',
  `treasure_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`badge_id`,`treasure_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `badge_treasure_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `badge_treasure_ibfk_2` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='What treasures are needed to get this badge';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banned_player`
--

DROP TABLE IF EXISTS `banned_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banned_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_id` int(11) DEFAULT NULL,
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL,
  `banned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `challenge`
--

DROP TABLE IF EXISTS `challenge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `challenge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `difficulty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'moderate',
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `player_type` enum('offense','defense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offense',
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The filename that will be provided to participants',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credential`
--

DROP TABLE IF EXISTS `credential`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credential` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pubtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password',
  `target_id` int(11) NOT NULL COMMENT 'A target system that this credential is for.',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offense',
  `stock` int(11) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service` (`service`,`target_id`,`username`,`password`),
  KEY `target_id` (`target_id`),
  CONSTRAINT `credential_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Credentials that participants can discover and claim points.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crl`
--

DROP TABLE IF EXISTS `crl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `crl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `csr` text COLLATE utf8mb4_unicode_ci,
  `crt` text COLLATE utf8mb4_unicode_ci,
  `txtcrt` text COLLATE utf8mb4_unicode_ci,
  `privkey` text COLLATE utf8mb4_unicode_ci,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debuglogs`
--

DROP TABLE IF EXISTS `debuglogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `debuglogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disabled_route`
--

DROP TABLE IF EXISTS `disabled_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `disabled_route` (
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`route`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Disabled controller actions for pUI';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `experience`
--

DROP TABLE IF EXISTS `experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_points` int(11) DEFAULT NULL,
  `max_points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-experience-points` (`min_points`,`max_points`),
  KEY `idx-experience-points-category` (`min_points`,`max_points`,`category`),
  KEY `idx-experience-category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `weight` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finding`
--

DROP TABLE IF EXISTS `finding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `finding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pubname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` text COLLATE utf8mb4_unicode_ci,
  `pubdescription` text COLLATE utf8mb4_unicode_ci COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT '-1',
  `protocol` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `port` smallint(5) unsigned DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `protocol` (`protocol`,`target_id`,`port`),
  KEY `target_id` (`target_id`),
  CONSTRAINT `finding_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Automatic findings based on network findings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `headshot`
--

DROP TABLE IF EXISTS `headshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `headshot` (
  `player_id` int(10) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`player_id`,`target_id`),
  KEY `idx-headshot-player_id` (`player_id`),
  KEY `idx-headshot-target_id` (`target_id`),
  CONSTRAINT `fk-headshot-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-headshot-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hint`
--

DROP TABLE IF EXISTS `hint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `player_type` enum('offense','defense','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'easy_points',
  `badge_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the badge_id',
  `finding_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the finding_id',
  `treasure_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the treasure_id',
  `question_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user answered the question_id',
  `points_user` int(11) DEFAULT NULL COMMENT 'Display this record after the user reaches these many points',
  `points_team` int(11) DEFAULT NULL COMMENT 'Display this record after the team reaches these many points',
  `timeafter` bigint(20) DEFAULT '0' COMMENT 'Display this hint after X seconds have been passed since the Start of the event',
  `active` tinyint(1) DEFAULT '1' COMMENT 'Set this hint as active or innactive',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_id` (`badge_id`,`finding_id`,`treasure_id`,`question_id`,`player_type`),
  KEY `finding_id` (`finding_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `hint_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hint_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hint_ibfk_3` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The hints for the game based on user type';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `infrastructure`
--

DROP TABLE IF EXISTS `infrastructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `infrastructure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Infrastructure elements';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `infrastructure_target`
--

DROP TABLE IF EXISTS `infrastructure_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `infrastructure_target` (
  `infrastructure_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`infrastructure_id`,`target_id`),
  UNIQUE KEY `target_id` (`target_id`),
  CONSTRAINT `infrastructure_target_ibfk_1` FOREIGN KEY (`infrastructure_id`) REFERENCES `infrastructure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `infrastructure_target_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Infrastructure/Target association';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instruction`
--

DROP TABLE IF EXISTS `instruction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_type` enum('offense','defense','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `weight` int(11) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The instructions for the game for each player type';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player level standings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration_red`
--

DROP TABLE IF EXISTS `migration_red`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_red` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `muisess`
--

DROP TABLE IF EXISTS `muisess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `muisess` (
  `id` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='mUI Sessions table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network`
--

DROP TABLE IF EXISTS `network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `public` tinyint(1) DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_player`
--

DROP TABLE IF EXISTS `network_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `network_player` (
  `network_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`network_id`,`player_id`),
  KEY `idx-network_player-network_id` (`network_id`),
  KEY `idx-network_player-player_id` (`player_id`),
  CONSTRAINT `fk-network_player-network_id` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-network_player-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_target`
--

DROP TABLE IF EXISTS `network_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `network_target` (
  `network_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`network_id`,`target_id`),
  KEY `idx-network_target-network_id` (`network_id`),
  KEY `idx-network_target-target_id` (`target_id`),
  CONSTRAINT `fk-network_target-network_id` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-network_target-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `archived` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-notification-player_id` (`player_id`),
  CONSTRAINT `fk-notification-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `objective`
--

DROP TABLE IF EXISTS `objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `objective` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_type` enum('offense','defense','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `weight` int(11) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The objectives for the game based on player type';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('offense','defense') COLLATE utf8mb4_unicode_ci DEFAULT 'offense',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'password',
  `activkey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `academic` tinyint(1) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auth_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx-player-status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_badge`
--

DROP TABLE IF EXISTS `player_badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_badge` (
  `player_id` int(10) unsigned NOT NULL,
  `badge_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`badge_id`),
  KEY `badge_id` (`badge_id`),
  CONSTRAINT `player_badge_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_badge_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The badges each user will get to see';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_finding`
--

DROP TABLE IF EXISTS `player_finding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_finding` (
  `player_id` int(10) unsigned NOT NULL,
  `finding_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`finding_id`),
  KEY `finding_id` (`finding_id`),
  CONSTRAINT `player_finding_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_finding_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The user findings each user will get to see';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_hint`
--

DROP TABLE IF EXISTS `player_hint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_hint` (
  `player_id` int(10) unsigned NOT NULL,
  `hint_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`hint_id`),
  KEY `hint_id` (`hint_id`),
  CONSTRAINT `player_hint_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_hint_ibfk_2` FOREIGN KEY (`hint_id`) REFERENCES `hint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The hints each user will get to see';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_last`
--

DROP TABLE IF EXISTS `player_last`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_last` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `on_pui` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `on_vpn` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vpn_remote_address` int(11) unsigned DEFAULT NULL,
  `vpn_local_address` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-player_last-on_pui` (`on_pui`),
  KEY `idx-player_last-on_vpn` (`on_vpn`),
  KEY `idx-player_last-vpn_remote_address` (`vpn_remote_address`),
  KEY `idx-player_last-vpn_local_address` (`vpn_local_address`),
  KEY `idx-player_last-combined` (`vpn_local_address`,`vpn_remote_address`,`on_vpn`,`on_pui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_question`
--

DROP TABLE IF EXISTS `player_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `player_question_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_question_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_rank`
--

DROP TABLE IF EXISTS `player_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_rank` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `player_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `idx-player_rank-player_id` (`player_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_score`
--

DROP TABLE IF EXISTS `player_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_score` (
  `player_id` int(10) unsigned NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`),
  KEY `idx-player_score-points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_spin`
--

DROP TABLE IF EXISTS `player_spin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_spin` (
  `player_id` int(11) unsigned NOT NULL,
  `counter` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`),
  KEY `idx-player_spin-player_id` (`player_id`),
  CONSTRAINT `fk-player_spin-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_ssl`
--

DROP TABLE IF EXISTS `player_ssl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_ssl` (
  `player_id` int(10) unsigned NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `csr` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `crt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `txtcrt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `privkey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`),
  CONSTRAINT `player_ssl_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player SSL Keys';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_treasure`
--

DROP TABLE IF EXISTS `player_treasure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_treasure` (
  `player_id` int(10) unsigned NOT NULL,
  `treasure_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`,`treasure_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `player_treasure_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_treasure_ibfk_2` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The treasures each user will get to see';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_tutorial_task`
--

DROP TABLE IF EXISTS `player_tutorial_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_tutorial_task` (
  `player_id` int(11) unsigned NOT NULL,
  `tutorial_task_dependency_id` int(11) NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`player_id`,`tutorial_task_dependency_id`),
  KEY `idx-player_tutorial_task-player_id` (`player_id`),
  KEY `idx-player_tutorial_task-tutorial_task_dependency_id` (`tutorial_task_dependency_id`),
  CONSTRAINT `fk-player_tutorial_task-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-player_tutorial_task-tutorial_task_dependency_id` FOREIGN KEY (`tutorial_task_dependency_id`) REFERENCES `tutorial_task_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_vpn_history`
--

DROP TABLE IF EXISTS `player_vpn_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_vpn_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `vpn_remote_address` int(11) unsigned DEFAULT NULL,
  `vpn_local_address` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `player_id` int(11) unsigned NOT NULL,
  `visibility` enum('public','private','ingame') COLLATE utf8mb4_unicode_ci DEFAULT 'private',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'UNK' COMMENT 'Country code (eg GR)',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png' COMMENT 'Profile avatar',
  `discord` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Discord handle (eg  @username#1234)',
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Twitter handle (eg @echoCTF)',
  `github` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Github handle (eg echoCTF)',
  `htb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'HTB Profile (eg 47396)',
  `terms_and_conditions` tinyint(1) DEFAULT '0' COMMENT 'Accepted Terms and Conditions?',
  `mail_optin` tinyint(1) DEFAULT '0' COMMENT 'Opt in for mail notifications?',
  `gdpr` tinyint(1) DEFAULT '0' COMMENT 'GDPR Acceptance?',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `idx-profile-gdpr` (`gdpr`),
  KEY `idx-profile-mail_optin` (`mail_optin`),
  KEY `idx-profile-terms_and_conditions` (`terms_and_conditions`),
  KEY `idx-profile-allboolean` (`gdpr`,`mail_optin`,`terms_and_conditions`),
  KEY `idx-profile-visibility` (`visibility`),
  KEY `idx-profile-country` (`country`),
  KEY `idx-profile-created_at` (`created_at`),
  KEY `idx-profile-updated_at` (`updated_at`),
  KEY `idx-profile-created-updated-at` (`created_at`,`updated_at`),
  KEY `idx-profile-multiple` (`visibility`,`country`,`id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challenge_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offense',
  `code` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenge_id` (`challenge_id`,`name`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `challenge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','invalid','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `points` int(11) NOT NULL DEFAULT '0',
  `modcomment` longtext COLLATE utf8mb4_unicode_ci COMMENT 'The comment from the moderator about the report',
  `pubtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pubbody` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rule`
--

DROP TABLE IF EXISTS `rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_type` enum('offense','defense','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `weight` int(11) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The rules for the game based on player type';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longblob,
  `player_id` int(10) unsigned DEFAULT NULL,
  `ip` bigint(20) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `expire` (`expire`),
  KEY `id` (`id`,`expire`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spin_history`
--

DROP TABLE IF EXISTS `spin_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-spin_history-target_id` (`target_id`),
  KEY `idx-spin_history-player_id` (`player_id`),
  CONSTRAINT `fk-spin_history-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-spin_history-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spin_queue`
--

DROP TABLE IF EXISTS `spin_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_queue` (
  `target_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`target_id`),
  KEY `idx-spin_queue-target_id` (`target_id`),
  KEY `idx-spin_queue-player_id` (`player_id`),
  CONSTRAINT `fk-spin_queue-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-spin_queue-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stream`
--

DROP TABLE IF EXISTS `stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stream` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `pubtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pubmessage` text COLLATE utf8mb4_unicode_ci,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `stream_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Information stream for participants and public alike';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sysconfig`
--

DROP TABLE IF EXISTS `sysconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sysconfig` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` longblob,
  `description` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Very simple table keeping single key/val pairs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `target`
--

DROP TABLE IF EXISTS `target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `target` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'target ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A name for the target',
  `fqdn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The FQDN for the target',
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The purpose of this target',
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip` int(10) unsigned NOT NULL COMMENT 'The IP of the target',
  `mac` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The mac associated with this IP',
  `active` tinyint(1) DEFAULT '1',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'offline',
  `scheduled_at` datetime DEFAULT NULL,
  `net` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Network this pod is attached',
  `server` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Docker Server connection string.',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `dns` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rootable` tinyint(1) DEFAULT '0' COMMENT 'Whether the target is rootable or not',
  `difficulty` int(11) DEFAULT '0',
  `suggested_xp` int(11) DEFAULT '0',
  `required_xp` int(11) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `fqdn` (`fqdn`),
  UNIQUE KEY `mac` (`mac`),
  KEY `idx-target-suggested_xp` (`suggested_xp`),
  KEY `idx-target-required_xp` (`required_xp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Keeps track of the CTF target IP and MAC addresses along with simple asset management';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `target_variable`
--

DROP TABLE IF EXISTS `target_variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `target_variable` (
  `target_id` int(11) NOT NULL COMMENT 'Docker this variable belongs to',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Variable key (aka name)',
  `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Variable value',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`target_id`,`key`),
  CONSTRAINT `target_variable_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Docker variables';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `target_volume`
--

DROP TABLE IF EXISTS `target_volume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `target_volume` (
  `target_id` int(11) NOT NULL COMMENT 'Docker this volume belongs to',
  `volume` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Volume on host to map',
  `bind` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bind to path within pod',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`target_id`,`volume`),
  CONSTRAINT `target_volume_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Docker volumes and binds';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `academic` tinyint(1) DEFAULT '0',
  `logo` longblob,
  `owner_id` int(11) unsigned NOT NULL,
  `token` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `token` (`token`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `team_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_player`
--

DROP TABLE IF EXISTS `team_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `approved` tinyint(1) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  UNIQUE KEY `team_id` (`team_id`,`player_id`),
  CONSTRAINT `team_player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `team_player_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_score`
--

DROP TABLE IF EXISTS `team_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_score` (
  `team_id` int(11) NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `treasure`
--

DROP TABLE IF EXISTS `treasure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `treasure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pubname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` text COLLATE utf8mb4_unicode_ci,
  `pubdescription` text COLLATE utf8mb4_unicode_ci COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offense',
  `csum` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'If there is a file attached to this treasure',
  `appears` int(11) DEFAULT '0',
  `effects` enum('player','team','total') COLLATE utf8mb4_unicode_ci DEFAULT 'player',
  `target_id` int(11) NOT NULL DEFAULT '0' COMMENT 'A target system that this treasure is hidden on. This is not required but its good to have',
  `code` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`target_id`,`code`,`csum`),
  UNIQUE KEY `code` (`code`),
  KEY `target_id` (`target_id`),
  CONSTRAINT `treasure_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Claimable points by the user, through hidden codes and files';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `treasure_action`
--

DROP TABLE IF EXISTS `treasure_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `treasure_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `treasure_id` int(11) NOT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `command` text COLLATE utf8mb4_unicode_ci,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `treasure_action_ibfk_1` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial`
--

DROP TABLE IF EXISTS `tutorial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_target`
--

DROP TABLE IF EXISTS `tutorial_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_target` (
  `tutorial_id` int(11) NOT NULL DEFAULT '0',
  `target_id` int(11) NOT NULL DEFAULT '0',
  `weight` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`tutorial_id`,`target_id`),
  KEY `idx-tutorial_target-tutorial_id` (`tutorial_id`),
  KEY `idx-tutorial_target-target_id` (`target_id`),
  CONSTRAINT `fk-tutorial_target-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-tutorial_target-tutorial_id` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_task`
--

DROP TABLE IF EXISTS `tutorial_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tutorial_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points` int(11) DEFAULT '0',
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-tutorial_task-tutorial_id` (`tutorial_id`),
  CONSTRAINT `fk-tutorial_task-tutorial_id` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_task_dependency`
--

DROP TABLE IF EXISTS `tutorial_task_dependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_task_dependency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tutorial_task_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-tutorial_task_dependency-tutorial_task_id` (`tutorial_task_id`),
  CONSTRAINT `fk-tutorial_task_dependency-tutorial_task_id` FOREIGN KEY (`tutorial_task_id`) REFERENCES `tutorial_task` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
