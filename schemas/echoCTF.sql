SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER DATABASE `echoCTF` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
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
CREATE TABLE `achievement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pubname` varchar(255) DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` mediumtext DEFAULT NULL,
  `pubdescription` mediumtext DEFAULT NULL COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') NOT NULL DEFAULT 'offense',
  `appears` int(11) DEFAULT 0,
  `effects` enum('users_id','team','total') DEFAULT 'users_id',
  `code` varchar(128) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `archived_stream`
--

DROP TABLE IF EXISTS `archived_stream`;
CREATE TABLE `archived_stream` (
  `id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `player_id` int(10) unsigned DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `message` mediumtext DEFAULT NULL,
  `pubtitle` varchar(255) NOT NULL,
  `pubmessage` mediumtext DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `avatar`
--

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE `avatar` (
  `id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `badge`
--

DROP TABLE IF EXISTS `badge`;
CREATE TABLE `badge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pubname` varchar(255) DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` mediumtext DEFAULT NULL,
  `pubdescription` mediumtext DEFAULT NULL COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Badges are given to the users based on gathering certain other achievements, treasures and findings';

--
-- Table structure for table `badge_finding`
--

DROP TABLE IF EXISTS `badge_finding`;
CREATE TABLE `badge_finding` (
  `badge_id` int(11) NOT NULL DEFAULT 0,
  `finding_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`badge_id`,`finding_id`),
  KEY `finding_id` (`finding_id`),
  CONSTRAINT `badge_finding_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `badge_finding_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='What treasures are needed to get this badge';

--
-- Table structure for table `badge_treasure`
--

DROP TABLE IF EXISTS `badge_treasure`;
CREATE TABLE `badge_treasure` (
  `badge_id` int(11) NOT NULL DEFAULT 0,
  `treasure_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`badge_id`,`treasure_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `badge_treasure_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `badge_treasure_ibfk_2` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='What treasures are needed to get this badge';

--
-- Table structure for table `banned_mx_server`
--

DROP TABLE IF EXISTS `banned_mx_server`;
CREATE TABLE `banned_mx_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `notes` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `banned_player`
--

DROP TABLE IF EXISTS `banned_player`;
CREATE TABLE `banned_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_id` int(11) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL,
  `banned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `challenge`
--

DROP TABLE IF EXISTS `challenge`;
CREATE TABLE `challenge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT '',
  `difficulty` varchar(255) DEFAULT 'moderate',
  `description` longtext DEFAULT NULL,
  `player_type` enum('offense','defense') NOT NULL DEFAULT 'offense',
  `filename` varchar(255) DEFAULT NULL COMMENT 'The filename that will be provided to participants',
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `icon` varchar(255) DEFAULT NULL,
  `timer` tinyint(1) DEFAULT 1,
  `public` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx-timer` (`timer`),
  KEY `idx-challenge-public` (`public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `challenge_solver`
--

DROP TABLE IF EXISTS `challenge_solver`;
CREATE TABLE `challenge_solver` (
  `challenge_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `timer` bigint(20) DEFAULT NULL,
  `rating` smallint(6) NOT NULL DEFAULT -1,
  `created_at` datetime DEFAULT NULL,
  `first` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`challenge_id`,`player_id`),
  KEY `idx-challenge_solver-challenge_id` (`challenge_id`),
  KEY `idx-challenge_solver-player_id` (`player_id`),
  KEY `idx-timer` (`timer`),
  CONSTRAINT `fk-challenge_solver-challenge_id` FOREIGN KEY (`challenge_id`) REFERENCES `challenge` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-challenge_solver-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` varchar(12) NOT NULL DEFAULT '',
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `credential`
--

DROP TABLE IF EXISTS `credential`;
CREATE TABLE `credential` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pubtitle` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) NOT NULL COMMENT 'Password',
  `target_id` int(11) NOT NULL COMMENT 'A target system that this credential is for.',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') NOT NULL DEFAULT 'offense',
  `stock` int(11) DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `service` (`service`,`target_id`,`username`,`password`),
  KEY `target_id` (`target_id`),
  CONSTRAINT `credential_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Credentials that participants can discover and claim points.';

--
-- Table structure for table `credits`
--

DROP TABLE IF EXISTS `credits`;
CREATE TABLE `credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `weight` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `crl`
--

DROP TABLE IF EXISTS `crl`;
CREATE TABLE `crl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `subject` mediumtext DEFAULT NULL,
  `csr` mediumtext DEFAULT NULL,
  `crt` mediumtext DEFAULT NULL,
  `privkey` mediumtext DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `debuglogs`
--

DROP TABLE IF EXISTS `debuglogs`;
CREATE TABLE `debuglogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `devnull`
--

DROP TABLE IF EXISTS `devnull`;
CREATE TABLE `devnull` (
  `silence` blob DEFAULT NULL
) ENGINE=BLACKHOLE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `disabled_route`
--

DROP TABLE IF EXISTS `disabled_route`;
CREATE TABLE `disabled_route` (
  `route` varchar(255) NOT NULL,
  PRIMARY KEY (`route`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Disabled controller actions for pUI';

--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
CREATE TABLE `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `html` mediumtext DEFAULT NULL,
  `txt` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `experience`
--

DROP TABLE IF EXISTS `experience`;
CREATE TABLE `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(32) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `min_points` int(11) DEFAULT NULL,
  `max_points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-experience-points` (`min_points`,`max_points`),
  KEY `idx-experience-points-category` (`min_points`,`max_points`,`category`),
  KEY `idx-experience-category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` mediumtext DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_faq_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `finding`
--

DROP TABLE IF EXISTS `finding`;
CREATE TABLE `finding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pubname` varchar(255) DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` mediumtext DEFAULT NULL,
  `pubdescription` mediumtext DEFAULT NULL COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT -1,
  `protocol` varchar(30) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `port` smallint(5) unsigned DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `protocol` (`protocol`,`target_id`,`port`),
  KEY `target_id` (`target_id`),
  CONSTRAINT `finding_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Automatic findings based on network findings';

--
-- Table structure for table `headshot`
--

DROP TABLE IF EXISTS `headshot`;
CREATE TABLE `headshot` (
  `player_id` int(10) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `timer` bigint(20) unsigned DEFAULT 0,
  `rating` smallint(6) DEFAULT -1,
  `first` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`player_id`,`target_id`),
  KEY `query-index` (`target_id`,`created_at`),
  KEY `idx-headshot-player_id` (`player_id`),
  KEY `idx-headshot-target_id` (`target_id`),
  CONSTRAINT `fk-headshot-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-headshot-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `hint`
--

DROP TABLE IF EXISTS `hint`;
CREATE TABLE `hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `player_type` enum('offense','defense','both') DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'easy_points',
  `badge_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the badge_id',
  `finding_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the finding_id',
  `treasure_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user received the treasure_id',
  `question_id` int(11) DEFAULT NULL COMMENT 'Display this record after the user answered the question_id',
  `points_user` int(11) DEFAULT NULL COMMENT 'Display this record after the user reaches these many points',
  `points_team` int(11) DEFAULT NULL COMMENT 'Display this record after the team reaches these many points',
  `timeafter` bigint(20) DEFAULT 0 COMMENT 'Display this hint after X seconds have been passed since the Start of the event',
  `active` tinyint(1) DEFAULT 1 COMMENT 'Set this hint as active or innactive',
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_id` (`badge_id`,`finding_id`,`treasure_id`,`question_id`,`player_type`),
  KEY `finding_id` (`finding_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `hint_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hint_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hint_ibfk_3` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The hints for the game based on user type';

--
-- Table structure for table `infrastructure`
--

DROP TABLE IF EXISTS `infrastructure`;
CREATE TABLE `infrastructure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Infrastructure elements';

--
-- Table structure for table `infrastructure_target`
--

DROP TABLE IF EXISTS `infrastructure_target`;
CREATE TABLE `infrastructure_target` (
  `infrastructure_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`infrastructure_id`,`target_id`),
  UNIQUE KEY `target_id` (`target_id`),
  CONSTRAINT `infrastructure_target_ibfk_1` FOREIGN KEY (`infrastructure_id`) REFERENCES `infrastructure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `infrastructure_target_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Infrastructure/Target association';

--
-- Table structure for table `init_data`
--

DROP TABLE IF EXISTS `init_data`;
CREATE TABLE `init_data` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Table structure for table `inquiry`
--

DROP TABLE IF EXISTS `inquiry`;
CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned NOT NULL,
  `answered` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(255) DEFAULT 'contact',
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `serialized` mediumtext DEFAULT NULL,
  `body` mediumtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-inquiry-player_id` (`player_id`),
  CONSTRAINT `fk-inquiry-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `instruction`
--

DROP TABLE IF EXISTS `instruction`;
CREATE TABLE `instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `player_type` enum('offense','defense','both') DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The instructions for the game for each player type';

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` varchar(8) NOT NULL,
  `l` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `layout_override`
--

DROP TABLE IF EXISTS `layout_override`;
CREATE TABLE `layout_override` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `route` varchar(255) DEFAULT NULL,
  `guest` tinyint(1) NOT NULL DEFAULT 0,
  `player_id` int(11) DEFAULT NULL,
  `css` text DEFAULT NULL,
  `js` text DEFAULT NULL,
  `repeating` tinyint(1) NOT NULL DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  KEY `query-index` (player_id,valid_from,valid_until, repeating),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
CREATE TABLE `level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `points` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player level standings';

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `migration_red`
--

DROP TABLE IF EXISTS `migration_red`;
CREATE TABLE `migration_red` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `migration_sales`
--

DROP TABLE IF EXISTS `migration_sales`;
CREATE TABLE `migration_sales` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `muisess`
--

DROP TABLE IF EXISTS `muisess`;
CREATE TABLE `muisess` (
  `id` char(40) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='mUI Sessions table';

--
-- Table structure for table `network`
--

DROP TABLE IF EXISTS `network`;
CREATE TABLE `network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `public` tinyint(1) DEFAULT 1,
  `guest` tinyint(1) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `announce` tinyint(1) DEFAULT 1,
  `codename` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx-network-weight` (`weight`),
  KEY `count-active-network` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `network_player`
--

DROP TABLE IF EXISTS `network_player`;
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

--
-- Table structure for table `network_target`
--

DROP TABLE IF EXISTS `network_target`;
CREATE TABLE `network_target` (
  `network_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `weight` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`network_id`,`target_id`),
  KEY `idx-network_target-network_id` (`network_id`),
  KEY `idx-network_target-target_id` (`target_id`),
  CONSTRAINT `fk-network_target-network_id` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-network_target-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `network_target_schedule`
--

DROP TABLE IF EXISTS `network_target_schedule`;
CREATE TABLE `network_target_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `network_id` int(11) DEFAULT NULL,
  `migration_date` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `query-index` (`target_id`,`migration_date`,`network_id`),
  KEY `idx-network_target_schedule-target_id` (`target_id`),
  KEY `idx-network_target_schedule-network_id` (`network_id`),
  CONSTRAINT `fk-network_target_schedule-network_id` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-network_target_schedule-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` mediumtext DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned NOT NULL,
  `category` varchar(20) NOT NULL DEFAULT 'success',
  `title` varchar(255) DEFAULT NULL,
  `body` mediumtext DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `query-index` (player_id,archived,created_at,id),
  KEY `idx-notification-player_id` (`player_id`),
  KEY `idx-notification-archived` (`archived`),
  KEY `idx-notification-category` (`category`),
  CONSTRAINT `fk-notification-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `objective`
--

DROP TABLE IF EXISTS `objective`;
CREATE TABLE `objective` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `player_type` enum('offense','defense','both') DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The objectives for the game based on player type';

--
-- Table structure for table `openvpn`
--

DROP TABLE IF EXISTS `openvpn`;
CREATE TABLE `openvpn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `net` int(11) unsigned DEFAULT NULL,
  `mask` int(11) unsigned DEFAULT NULL,
  `management_ip` int(11) unsigned DEFAULT NULL,
  `management_port` smallint(6) unsigned DEFAULT NULL,
  `management_passwd` varchar(255) DEFAULT NULL,
  `status_log` varchar(255) DEFAULT NULL,
  `conf` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `server_name_net` (`server`,`name`,`net`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metatags` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `type` enum('offense','defense') DEFAULT 'offense',
  `password` varchar(255) NOT NULL DEFAULT 'password',
  `activkey` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `academic` tinyint(1) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `admin` smallint(6) DEFAULT 0,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `approval` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `player_email_idx` (`email`),
  KEY `idx-player-status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_badge`
--

DROP TABLE IF EXISTS `player_badge`;
CREATE TABLE `player_badge` (
  `player_id` int(10) unsigned NOT NULL,
  `badge_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`player_id`,`badge_id`),
  KEY `badge_id` (`badge_id`),
  CONSTRAINT `player_badge_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_badge_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The badges each user will get to see';

--
-- Table structure for table `player_counter_nf`
--

DROP TABLE IF EXISTS `player_counter_nf`;
CREATE TABLE `player_counter_nf` (
  `player_id` int(11) NOT NULL,
  `metric` varchar(255) NOT NULL,
  `counter` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`player_id`,`metric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_country_rank`
--

DROP TABLE IF EXISTS `player_country_rank`;
CREATE TABLE `player_country_rank` (
  `id` int(11) NOT NULL DEFAULT 0,
  `player_id` int(11) unsigned NOT NULL,
  `country` varchar(3) NOT NULL,
  PRIMARY KEY (`id`,`country`),
  UNIQUE KEY `idx-player_country_rank-player_id` (`player_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_disabledroute`
--

DROP TABLE IF EXISTS `player_disabledroute`;
CREATE TABLE `player_disabledroute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-player_disabledroute-player_id` (`player_id`),
  KEY `idx-player_disabledroute-route` (`route`),
  CONSTRAINT `fk-player_disabledroute-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_disconnect_queue`
--

DROP TABLE IF EXISTS `player_disconnect_queue`;
CREATE TABLE `player_disconnect_queue` (
  `player_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`player_id`),
  CONSTRAINT `fk-player_id-player` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_disconnect_queue_history`
--

DROP TABLE IF EXISTS `player_disconnect_queue_history`;
CREATE TABLE `player_disconnect_queue_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_finding`
--

DROP TABLE IF EXISTS `player_finding`;
CREATE TABLE `player_finding` (
  `player_id` int(10) unsigned NOT NULL,
  `finding_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` float DEFAULT NULL,
  PRIMARY KEY (`player_id`,`finding_id`),
  KEY `finding_id` (`finding_id`),
  CONSTRAINT `player_finding_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_finding_ibfk_2` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The user findings each user will get to see';

--
-- Table structure for table `player_hint`
--

DROP TABLE IF EXISTS `player_hint`;
CREATE TABLE `player_hint` (
  `player_id` int(10) unsigned NOT NULL,
  `hint_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`player_id`,`hint_id`),
  KEY `hint_id` (`hint_id`),
  CONSTRAINT `player_hint_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_hint_ibfk_2` FOREIGN KEY (`hint_id`) REFERENCES `hint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The hints each user will get to see';

--
-- Table structure for table `player_last`
--

DROP TABLE IF EXISTS `player_last`;
CREATE TABLE `player_last` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `on_pui` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `on_vpn` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vpn_remote_address` int(11) unsigned DEFAULT NULL,
  `vpn_local_address` int(11) unsigned DEFAULT NULL,
  `signup_ip` int(10) unsigned DEFAULT NULL,
  `signin_ip` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-player_last-on_pui` (`on_pui`),
  KEY `idx-player_last-on_vpn` (`on_vpn`),
  KEY `idx-player_last-vpn_remote_address` (`vpn_remote_address`),
  KEY `idx-player_last-vpn_local_address` (`vpn_local_address`),
  KEY `idx-player_last-combined` (`vpn_local_address`,`vpn_remote_address`,`on_vpn`,`on_pui`),
  KEY `idx-player_last-signup_ip` (`signup_ip`),
  KEY `idx-player_last-signin_ip` (`signin_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_metadata`
--

DROP TABLE IF EXISTS `player_metadata`;
CREATE TABLE `player_metadata` (
  `player_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identificationFile` varchar(64) DEFAULT NULL,
  `affiliation` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `idx-player_metadata-player_id` (`player_id`),
  CONSTRAINT `fk-player_metadata-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_question`
--

DROP TABLE IF EXISTS `player_question`;
CREATE TABLE `player_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx-player_question-player_id-question_id` (`question_id`,`player_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `player_question_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_question_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_rank`
--

DROP TABLE IF EXISTS `player_rank`;
CREATE TABLE `player_rank` (
  `id` int(11) unsigned NOT NULL DEFAULT 0,
  `player_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`player_id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `idx-player_rank-player_id` (`player_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_relation`
--

DROP TABLE IF EXISTS `player_relation`;
CREATE TABLE `player_relation` (
  `player_id` int(11) unsigned NOT NULL,
  `referred_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `idx-player_relation-player_id` (`player_id`),
  KEY `idx-player_relation-referred_id` (`referred_id`),
  CONSTRAINT `fk-player_relation-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_score`
--

DROP TABLE IF EXISTS `player_score`;
CREATE TABLE `player_score` (
  `player_id` int(10) unsigned NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`player_id`),
  KEY `idx-player_score-points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_score_monthly`
--

DROP TABLE IF EXISTS `player_score_monthly`;
CREATE TABLE `player_score_monthly` (
  `player_id` int(11) NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT 0,
  `dated_at` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`player_id`,`dated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_spin`
--

DROP TABLE IF EXISTS `player_spin`;
CREATE TABLE `player_spin` (
  `player_id` int(11) unsigned NOT NULL,
  `counter` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `perday` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `idx-player_spin-player_id` (`player_id`),
  CONSTRAINT `fk-player_spin-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_ssl`
--

DROP TABLE IF EXISTS `player_ssl`;
CREATE TABLE `player_ssl` (
  `player_id` int(10) unsigned NOT NULL,
  `serial` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` mediumtext NOT NULL,
  `csr` mediumtext NOT NULL,
  `crt` mediumtext NOT NULL,
  `privkey` mediumtext NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`player_id`),
  UNIQUE KEY `serial` (`serial`),
  CONSTRAINT `player_ssl_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player SSL Keys';

--
-- Table structure for table `player_target_help`
--

DROP TABLE IF EXISTS `player_target_help`;
CREATE TABLE `player_target_help` (
  `player_id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`player_id`,`target_id`),
  KEY `idx-player_target_help-player_id` (`player_id`),
  KEY `idx-player_target_help-target_id` (`target_id`),
  CONSTRAINT `fk-player_target_help-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-player_target_help-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_token`
--

DROP TABLE IF EXISTS `player_token`;
CREATE TABLE `player_token` (
  `player_id` int(11) unsigned NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'API',
  `token` varchar(128) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`player_id`,`type`),
  UNIQUE KEY `token` (`token`),
  CONSTRAINT `fk-player_token-player_id-player` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_token_history`
--

DROP TABLE IF EXISTS `player_token_history`;
CREATE TABLE `player_token_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'API',
  `token` varchar(128) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `fk-player_token_history-player_id-player` (`player_id`),
  CONSTRAINT `fk-player_token_history-player_id-player` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_treasure`
--

DROP TABLE IF EXISTS `player_treasure`;
CREATE TABLE `player_treasure` (
  `player_id` int(10) unsigned NOT NULL,
  `treasure_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` float DEFAULT NULL,
  PRIMARY KEY (`player_id`,`treasure_id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `player_treasure_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_treasure_ibfk_2` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The treasures each user will get to see';

--
-- Table structure for table `player_tutorial_task`
--

DROP TABLE IF EXISTS `player_tutorial_task`;
CREATE TABLE `player_tutorial_task` (
  `player_id` int(11) unsigned NOT NULL,
  `tutorial_task_dependency_id` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`player_id`,`tutorial_task_dependency_id`),
  KEY `idx-player_tutorial_task-player_id` (`player_id`),
  KEY `idx-player_tutorial_task-tutorial_task_dependency_id` (`tutorial_task_dependency_id`),
  CONSTRAINT `fk-player_tutorial_task-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-player_tutorial_task-tutorial_task_dependency_id` FOREIGN KEY (`tutorial_task_dependency_id`) REFERENCES `tutorial_task_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `player_vpn_history`
--

DROP TABLE IF EXISTS `player_vpn_history`;
CREATE TABLE `player_vpn_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `vpn_remote_address` int(11) unsigned DEFAULT NULL,
  `vpn_local_address` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `player_id` int(11) unsigned NOT NULL,
  `visibility` enum('public','private','ingame') DEFAULT 'private',
  `bio` mediumtext DEFAULT NULL,
  `country` varchar(3) DEFAULT 'UNK' COMMENT 'Country code (eg GR)',
  `avatar` varchar(255) DEFAULT 'default.png' COMMENT 'Profile avatar',
  `discord` varchar(255) DEFAULT '' COMMENT 'Discord handle (eg  @username#1234)',
  `twitter` varchar(255) DEFAULT '' COMMENT 'Twitter handle (eg @echoCTF)',
  `github` varchar(255) DEFAULT '' COMMENT 'Github handle (eg echoCTF)',
  `htb` varchar(255) DEFAULT '' COMMENT 'HTB Profile (eg 47396)',
  `terms_and_conditions` tinyint(1) DEFAULT 0 COMMENT 'Accepted Terms and Conditions?',
  `mail_optin` tinyint(1) DEFAULT 0 COMMENT 'Opt in for mail notifications?',
  `gdpr` tinyint(1) DEFAULT 0 COMMENT 'GDPR Acceptance?',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `approved_avatar` tinyint(1) DEFAULT 1,
  `youtube` varchar(255) DEFAULT NULL,
  `twitch` varchar(255) DEFAULT NULL,
  `echoctf` int(11) unsigned DEFAULT NULL,
  `pending_progress` tinyint(1) NOT NULL DEFAULT 1,
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

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challenge_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') NOT NULL DEFAULT 'offense',
  `code` varchar(128) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `parent` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenge_id` (`challenge_id`,`name`),
  KEY `order-query-index` (`challenge_id`,`weight`,`id`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `challenge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `body` longtext DEFAULT NULL,
  `status` enum('pending','invalid','approved') NOT NULL DEFAULT 'pending',
  `points` int(11) NOT NULL DEFAULT 0,
  `modcomment` longtext DEFAULT NULL COMMENT 'The comment from the moderator about the report',
  `pubtitle` varchar(255) DEFAULT NULL,
  `pubbody` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `rule`
--

DROP TABLE IF EXISTS `rule`;
CREATE TABLE `rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `player_type` enum('offense','defense','both') DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The rules for the game based on player type';

--
-- Table structure for table `server`
--

DROP TABLE IF EXISTS `server`;
CREATE TABLE `server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `network` varchar(32) NOT NULL,
  `ip` int(11) unsigned NOT NULL,
  `description` text DEFAULT NULL,
  `service` enum('docker') NOT NULL DEFAULT 'docker',
  `connstr` varchar(255) NOT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `ssl` tinyint(1) NOT NULL DEFAULT 0,
  `timeout` int(11) NOT NULL DEFAULT 9000,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longblob DEFAULT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  `ip` bigint(20) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `expire` (`expire`),
  KEY `id` (`id`,`expire`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `speed_problem`
--

DROP TABLE IF EXISTS `speed_problem`;
CREATE TABLE `speed_problem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `difficulty` smallint(6) NOT NULL DEFAULT 0,
  `category` varchar(64) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `challenge_image` varchar(255) DEFAULT NULL,
  `validator_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `speed_solution`
--

DROP TABLE IF EXISTS `speed_solution`;
CREATE TABLE `speed_solution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) unsigned NOT NULL,
  `problem_id` int(11) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `sourcecode` longblob DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-speed_solution-player_id` (`player_id`),
  KEY `idx-speed_solution-problem_id` (`problem_id`),
  CONSTRAINT `fk-speed_solution-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-speed_solution-problem_id` FOREIGN KEY (`problem_id`) REFERENCES `speed_problem` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `spin_history`
--

DROP TABLE IF EXISTS `spin_history`;
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

--
-- Table structure for table `spin_queue`
--

DROP TABLE IF EXISTS `spin_queue`;
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

--
-- Table structure for table `stream`
--

DROP TABLE IF EXISTS `stream`;
CREATE TABLE `stream` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `message` mediumtext DEFAULT NULL,
  `pubtitle` varchar(255) NOT NULL,
  `pubmessage` mediumtext DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `query-index` (ts,id,model_id,model(20)),
  KEY `player_id` (`player_id`),
  CONSTRAINT `stream_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Information stream for participants and public alike';

--
-- Table structure for table `sysconfig`
--

DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `id` varchar(255) NOT NULL,
  `val` longblob DEFAULT NULL,
  `description` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Very simple table keeping single key/val pairs';

--
-- Table structure for table `target`
--

DROP TABLE IF EXISTS `target`;
CREATE TABLE `target` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'target ID',
  `name` varchar(255) DEFAULT NULL COMMENT 'A name for the target',
  `fqdn` varchar(255) DEFAULT NULL COMMENT 'The FQDN for the target',
  `purpose` varchar(255) DEFAULT NULL COMMENT 'The purpose of this target',
  `description` mediumtext DEFAULT NULL,
  `ip` int(10) unsigned NOT NULL COMMENT 'The IP of the target',
  `mac` varchar(30) DEFAULT NULL COMMENT 'The mac associated with this IP',
  `active` tinyint(1) DEFAULT 1,
  `status` varchar(32) DEFAULT 'offline',
  `scheduled_at` datetime DEFAULT NULL,
  `net` varchar(255) DEFAULT NULL COMMENT 'Network this pod is attached',
  `server` varchar(255) DEFAULT NULL COMMENT 'Docker Server connection string.',
  `image` varchar(255) DEFAULT '',
  `dns` varchar(255) DEFAULT NULL,
  `parameters` mediumtext DEFAULT NULL CHECK (json_valid(`parameters`)),
  `rootable` tinyint(1) DEFAULT 0 COMMENT 'Whether the target is rootable or not',
  `difficulty` int(11) DEFAULT 0,
  `suggested_xp` int(11) DEFAULT 0,
  `required_xp` int(11) DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `timer` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `healthcheck` tinyint(1) NOT NULL DEFAULT 1,
  `category` varchar(255) DEFAULT NULL,
  `imageparams` mediumtext DEFAULT NULL CHECK (json_valid(`imageparams`)),
  `writeup_allowed` int(11) DEFAULT 1,
  `player_spin` tinyint(1) DEFAULT 1,
  `headshot_spin` tinyint(1) DEFAULT 1,
  `instance_allowed` smallint(6) DEFAULT 1,
  `require_findings` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `fqdn` (`fqdn`),
  UNIQUE KEY `mac` (`mac`),
  KEY `idx-target-suggested_xp` (`suggested_xp`),
  KEY `idx-target-required_xp` (`required_xp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Keeps track of the CTF target IP and MAC addresses along with simple asset management';

--
-- Table structure for table `target_instance`
--

DROP TABLE IF EXISTS `target_instance`;
CREATE TABLE `target_instance` (
  `player_id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `ip` int(11) unsigned DEFAULT 0,
  `reboot` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `team_allowed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`player_id`),
  KEY `idx-target_instance-target_id` (`target_id`),
  CONSTRAINT `fk-target_instance-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-target_instance-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_instance_audit`
--

DROP TABLE IF EXISTS `target_instance_audit`;
CREATE TABLE `target_instance_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `op` char(1) NOT NULL DEFAULT 'i',
  `player_id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `ip` int(11) unsigned DEFAULT 0,
  `reboot` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `team_allowed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-target_instance_audit-player_id` (`player_id`),
  KEY `idx-target_instance_audit-target_id` (`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_metadata`
--

DROP TABLE IF EXISTS `target_metadata`;
CREATE TABLE `target_metadata` (
  `target_id` int(11) NOT NULL AUTO_INCREMENT,
  `scenario` mediumtext DEFAULT NULL,
  `instructions` mediumtext DEFAULT NULL,
  `solution` mediumtext DEFAULT NULL,
  `pre_credits` mediumtext DEFAULT NULL,
  `post_credits` mediumtext DEFAULT NULL,
  `pre_exploitation` mediumtext DEFAULT NULL,
  `post_exploitation` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`target_id`),
  CONSTRAINT `fk-target_metadata-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_ondemand`
--

DROP TABLE IF EXISTS `target_ondemand`;
CREATE TABLE `target_ondemand` (
  `target_id` int(11) NOT NULL,
  `player_id` int(11) unsigned DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT -1,
  `heartbeat` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`target_id`),
  KEY `idx-target_ondemand-target_id` (`target_id`),
  KEY `idx-target_ondemand-player_id` (`player_id`),
  CONSTRAINT `fk-target_ondemand-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk-target_ondemand-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_player_state`
--

DROP TABLE IF EXISTS `target_player_state`;
CREATE TABLE `target_player_state` (
  `id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `player_treasures` int(11) NOT NULL DEFAULT 0,
  `player_findings` int(11) NOT NULL DEFAULT 0,
  `player_points` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`player_id`),
  KEY `fk-target_player_state-player_id` (`player_id`),
  KEY `idx-target_player_state-player_treasures` (`player_treasures`),
  KEY `idx-target_player_state-player_findings` (`player_findings`),
  CONSTRAINT `fk-target_player_state-id` FOREIGN KEY (`id`) REFERENCES `target` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-target_player_state-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_state`
--

DROP TABLE IF EXISTS `target_state`;
CREATE TABLE `target_state` (
  `id` int(11) NOT NULL,
  `total_headshots` int(11) unsigned NOT NULL DEFAULT 0,
  `total_findings` int(11) unsigned NOT NULL DEFAULT 0,
  `total_treasures` int(11) unsigned NOT NULL DEFAULT 0,
  `player_rating` int(11) NOT NULL DEFAULT -1,
  `timer_avg` int(11) unsigned NOT NULL DEFAULT 0,
  `total_writeups` int(11) unsigned NOT NULL DEFAULT 0,
  `approved_writeups` int(11) unsigned NOT NULL DEFAULT 0,
  `finding_points` int(11) unsigned NOT NULL DEFAULT 0,
  `treasure_points` int(11) unsigned NOT NULL DEFAULT 0,
  `total_points` int(11) unsigned NOT NULL DEFAULT 0,
  `on_network` smallint(6) unsigned NOT NULL DEFAULT 0,
  `on_ondemand` smallint(6) unsigned NOT NULL DEFAULT 0,
  `ondemand_state` smallint(6) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`),
  KEY `idx-target_state-total_headshots` (`total_headshots`),
  KEY `idx-target_state-total_findings` (`total_findings`),
  KEY `idx-target_state-total_treasures` (`total_treasures`),
  KEY `idx-target_state-player_rating` (`player_rating`),
  KEY `idx-target_state-timer_avg` (`timer_avg`),
  CONSTRAINT `fk-target_states-id` FOREIGN KEY (`id`) REFERENCES `target` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `target_variable`
--

DROP TABLE IF EXISTS `target_variable`;
CREATE TABLE `target_variable` (
  `target_id` int(11) NOT NULL COMMENT 'Docker this variable belongs to',
  `key` varchar(255) NOT NULL COMMENT 'Variable key (aka name)',
  `val` varchar(255) NOT NULL COMMENT 'Variable value',
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`target_id`,`key`),
  CONSTRAINT `target_variable_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Docker variables';

--
-- Table structure for table `target_volume`
--

DROP TABLE IF EXISTS `target_volume`;
CREATE TABLE `target_volume` (
  `target_id` int(11) NOT NULL COMMENT 'Docker this volume belongs to',
  `volume` varchar(255) NOT NULL COMMENT 'Volume on host to map',
  `bind` varchar(255) NOT NULL COMMENT 'Bind to path within pod',
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`target_id`,`volume`),
  CONSTRAINT `target_volume_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Docker volumes and binds';

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `academic` tinyint(1) DEFAULT 0,
  `logo` longblob DEFAULT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `token` varchar(30) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `inviteonly` tinyint(1) NOT NULL DEFAULT 1,
  `recruitment` varchar(255) DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `token` (`token`),
  KEY `owner_id` (`owner_id`),
  KEY `idx-academic` (`academic`),
  KEY `idx-ts` (`ts`),
  CONSTRAINT `team_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_audit`
--

DROP TABLE IF EXISTS `team_audit`;
CREATE TABLE `team_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `action` varchar(20) NOT NULL DEFAULT 'default',
  `message` text DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `team_id_idx` (`team_id`),
  KEY `player_id_idx` (`player_id`),
  KEY `action_idx` (`action`),
  KEY `ts_idx` (`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_invite`
--

DROP TABLE IF EXISTS `team_invite`;
CREATE TABLE `team_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT NULL,
  `token` varchar(32) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `fk_team_id` (`team_id`),
  CONSTRAINT `fk_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_player`
--

DROP TABLE IF EXISTS `team_player`;
CREATE TABLE `team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) unsigned NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx-approved` (`approved`),
  KEY `idx-ts` (`ts`),
  UNIQUE KEY `player_id` (`player_id`),
  UNIQUE KEY `team_id` (`team_id`,`player_id`),
  CONSTRAINT `team_player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `team_player_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_rank`
--

DROP TABLE IF EXISTS `team_rank`;
CREATE TABLE `team_rank` (
  `id` int(11) NOT NULL DEFAULT 0,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`team_id`),
  UNIQUE KEY `team_id` (`team_id`),
  KEY `idx-team_rank-team_id` (`team_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_score`
--

DROP TABLE IF EXISTS `team_score`;
CREATE TABLE `team_score` (
  `team_id` int(11) NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `team_stream`
--

DROP TABLE IF EXISTS `team_stream`;
CREATE TABLE `team_stream` (
  `team_id` int(11) NOT NULL,
  `model` varchar(255) NOT NULL DEFAULT '',
  `model_id` int(11) NOT NULL DEFAULT 0,
  `points` float NOT NULL DEFAULT 0,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`team_id`,`model`,`model_id`),
  KEY `idx-team_stream-team_id` (`team_id`),
  KEY `idx-team_stream-model` (`model`),
  KEY `idx-team_stream-model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `treasure`
--

DROP TABLE IF EXISTS `treasure`;
CREATE TABLE `treasure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pubname` varchar(255) DEFAULT NULL COMMENT 'Name for the public eyes',
  `description` mediumtext DEFAULT NULL,
  `pubdescription` mediumtext DEFAULT NULL COMMENT 'Description for the public eyes',
  `points` decimal(10,2) DEFAULT NULL,
  `player_type` enum('offense','defense') NOT NULL DEFAULT 'offense',
  `csum` varchar(128) NOT NULL DEFAULT '' COMMENT 'If there is a file attached to this treasure',
  `appears` int(11) DEFAULT 0,
  `effects` enum('player','team','total') DEFAULT 'player',
  `target_id` int(11) NOT NULL DEFAULT 0 COMMENT 'A target system that this treasure is hidden on. This is not required but its good to have',
  `code` varchar(128) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(255) NOT NULL DEFAULT 'other',
  `location` varchar(255) DEFAULT NULL,
  `suggestion` mediumtext DEFAULT NULL,
  `solution` longtext DEFAULT NULL,
  `weight` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`target_id`,`code`,`csum`),
  UNIQUE KEY `code` (`code`),
  KEY `target_id` (`target_id`),
  KEY `idx-treasure-weight` (`weight`),
  KEY `query-index` (`target_id`,`weight`,`id`),
  CONSTRAINT `treasure_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Claimable points by the user, through hidden codes and files';

--
-- Table structure for table `treasure_action`
--

DROP TABLE IF EXISTS `treasure_action`;
CREATE TABLE `treasure_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `treasure_id` int(11) NOT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `command` mediumtext DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `treasure_id` (`treasure_id`),
  CONSTRAINT `treasure_action_ibfk_1` FOREIGN KEY (`treasure_id`) REFERENCES `treasure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `tutorial`
--

DROP TABLE IF EXISTS `tutorial`;
CREATE TABLE `tutorial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `tutorial_target`
--

DROP TABLE IF EXISTS `tutorial_target`;
CREATE TABLE `tutorial_target` (
  `tutorial_id` int(11) NOT NULL DEFAULT 0,
  `target_id` int(11) NOT NULL DEFAULT 0,
  `weight` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`tutorial_id`,`target_id`),
  KEY `idx-tutorial_target-tutorial_id` (`tutorial_id`),
  KEY `idx-tutorial_target-target_id` (`target_id`),
  CONSTRAINT `fk-tutorial_target-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-tutorial_target-tutorial_id` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `tutorial_task`
--

DROP TABLE IF EXISTS `tutorial_task`;
CREATE TABLE `tutorial_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tutorial_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `answer` varchar(255) DEFAULT NULL,
  `weight` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-tutorial_task-tutorial_id` (`tutorial_id`),
  CONSTRAINT `fk-tutorial_task-tutorial_id` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `tutorial_task_dependency`
--

DROP TABLE IF EXISTS `tutorial_task_dependency`;
CREATE TABLE `tutorial_task_dependency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tutorial_task_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-tutorial_task_dependency-tutorial_task_id` (`tutorial_task_id`),
  CONSTRAINT `fk-tutorial_task_dependency-tutorial_task_id` FOREIGN KEY (`tutorial_task_id`) REFERENCES `tutorial_task` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `url_route`
--

DROP TABLE IF EXISTS `url_route`;
CREATE TABLE `url_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `vpn_template`
--

DROP TABLE IF EXISTS `vpn_template`;
CREATE TABLE `vpn_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `client` tinyint(1) NOT NULL DEFAULT 1,
  `server` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `filename` varchar(255) NOT NULL DEFAULT 'echoCTF.ovpn',
  `description` mediumtext DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `query-index` (active,visible,client,`name`(60))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `writeup`
--

DROP TABLE IF EXISTS `writeup`;
CREATE TABLE `writeup` (
  `player_id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  `content` longblob DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `status` enum('PENDING','NEEDS FIXES','REJECTED','OK') DEFAULT NULL,
  `comment` longblob DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `formatter` varchar(255) NOT NULL DEFAULT 'text',
  `language_id` varchar(8) NOT NULL DEFAULT 'en',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`player_id`,`target_id`),
  UNIQUE KEY `id` (`id`),
  KEY `query-index` (target_id,approved,created_at),
  KEY `idx-writeup-player_id` (`player_id`),
  KEY `idx-writeup-target_id` (`target_id`),
  KEY `fk_language_id` (`language_id`),
  CONSTRAINT `fk-writeup-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-writeup-target_id` FOREIGN KEY (`target_id`) REFERENCES `target` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `writeup_rating`
--

DROP TABLE IF EXISTS `writeup_rating`;
CREATE TABLE `writeup_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `writeup_id` int(11) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `rating` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `writeup_id` (`writeup_id`,`player_id`),
  KEY `idx-writeup_rating-writeup_id` (`writeup_id`),
  KEY `idx-writeup_rating-player_id` (`player_id`),
  CONSTRAINT `fk-writeup_rating-player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-writeup_rating-writeup_id` FOREIGN KEY (`writeup_id`) REFERENCES `writeup` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
