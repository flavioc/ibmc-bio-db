-- MySQL dump 10.13  Distrib 5.1.32, for apple-darwin9.5.0 (i386)
--
-- Host: localhost    Database: FDB
-- ------------------------------------------------------
-- Server version	5.1.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `configuration` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'User ID.',
  `key` char(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Configuration Key.',
  `value` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Configuration value.',
  PRIMARY KEY (`user_id`,`key`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `configuration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Configuration table.';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `creation_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User that created this.',
  `creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/Time of creation.',
  `update_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User that modified this.',
  `update` timestamp NULL DEFAULT NULL COMMENT 'Date/Time of last modification.',
  PRIMARY KEY (`id`),
  KEY `creation_user_id` (`creation_user_id`),
  KEY `update_user_id` (`update_user_id`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`creation_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `history_ibfk_2` FOREIGN KEY (`update_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2796 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='history';
SET character_set_client = @saved_cs_client;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `add_history_trigger` BEFORE INSERT ON `history` FOR EACH ROW BEGIN
    SET NEW.`update` = NOW();
    SET NEW.`creation_user_id` = NEW.`update_user_id`;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `update_history_trigger` BEFORE UPDATE ON `history` FOR EACH ROW BEGIN
    set @author = OLD.creation_user_id;
    IF (@author IS NULL) THEN
      SET NEW.creation_user_id = NEW.update_user_id;
    END IF;
    SET NEW.`update` = NOW();
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `history_info`
--

DROP TABLE IF EXISTS `history_info`;
/*!50001 DROP VIEW IF EXISTS `history_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `history_info` (
  `history_id` bigint(20) unsigned,
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `label` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `type` enum('integer','text','obj','position','ref','tax','url','bool') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Label type.',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Label type name.',
  `comment` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Label type comment.',
  `autoadd` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Auto add to new sequences?',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is this a default label?',
  `must_exist` tinyint(1) NOT NULL COMMENT 'If this label must exist in all sequences.',
  `auto_on_creation` tinyint(1) NOT NULL COMMENT 'Generate label on creation.',
  `auto_on_modification` tinyint(1) NOT NULL COMMENT 'Generate label on modification.',
  `code` text COLLATE utf8_unicode_ci COMMENT 'Code to generate label.',
  `valid_code` text COLLATE utf8_unicode_ci COMMENT 'Check''s label validity.',
  `deletable` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Can this label be deleted?',
  `editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If true label sequence data can be edited.',
  `multiple` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If the label can be used multiple times per sequence.',
  `public` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is this label public?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `label_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label types.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_info_history`
--

DROP TABLE IF EXISTS `label_info_history`;
/*!50001 DROP VIEW IF EXISTS `label_info_history`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `label_info_history` (
  `history_id` bigint(20) unsigned,
  `label_id` bigint(20) unsigned,
  `type` enum('integer','text','obj','position','ref','tax','url','bool'),
  `name` char(255),
  `autoadd` tinyint(1),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` text,
  `deletable` tinyint(1),
  `editable` tinyint(1),
  `multiple` tinyint(1),
  `comment` varchar(1024),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1),
  `valid_code` text,
  `public` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_norm`
--

DROP TABLE IF EXISTS `label_norm`;
/*!50001 DROP VIEW IF EXISTS `label_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `label_norm` (
  `label_id` bigint(20) unsigned,
  `type` enum('integer','text','obj','position','ref','tax','url','bool'),
  `name` char(255),
  `autoadd` tinyint(1),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` text,
  `valid_code` text,
  `deletable` tinyint(1),
  `editable` tinyint(1),
  `multiple` tinyint(1),
  `comment` varchar(1024),
  `history_id` bigint(20) unsigned,
  `public` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `label_sequence`
--

DROP TABLE IF EXISTS `label_sequence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `label_sequence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `seq_id` bigint(20) unsigned NOT NULL COMMENT 'Sequence id.',
  `label_id` bigint(20) unsigned NOT NULL COMMENT 'Label id.',
  `subname` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Sub name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `int_data` int(11) DEFAULT NULL COMMENT 'Integer data.',
  `text_data` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Text data.',
  `obj_data` longblob COMMENT 'Object data.',
  `ref_data` bigint(20) unsigned DEFAULT NULL COMMENT 'Reference Data.',
  `position_a_data` int(11) DEFAULT NULL COMMENT 'Position data A.',
  `position_b_data` int(11) DEFAULT NULL COMMENT 'Position data B.',
  `taxonomy_data` bigint(20) unsigned DEFAULT NULL COMMENT 'Taxonomy data.',
  `url_data` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL label data.',
  `bool_data` tinyint(1) DEFAULT NULL COMMENT 'Boolean data.',
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`),
  KEY `label_id` (`label_id`),
  KEY `seq_id` (`seq_id`),
  KEY `ref_index` (`ref_data`),
  KEY `taxonomy_index` (`taxonomy_data`),
  CONSTRAINT `label_sequence_ibfk_5` FOREIGN KEY (`taxonomy_data`) REFERENCES `taxonomy` (`id`) ON DELETE SET NULL,
  CONSTRAINT `label_sequence_ibfk_1` FOREIGN KEY (`seq_id`) REFERENCES `sequence` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_sequence_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_sequence_ibfk_3` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL,
  CONSTRAINT `label_sequence_ibfk_4` FOREIGN KEY (`ref_data`) REFERENCES `sequence` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2156 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label''s of sequences.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_sequence_extra`
--

DROP TABLE IF EXISTS `label_sequence_extra`;
/*!50001 DROP VIEW IF EXISTS `label_sequence_extra`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `label_sequence_extra` (
  `id` bigint(20) unsigned,
  `seq_id` bigint(20) unsigned,
  `label_id` bigint(20) unsigned,
  `subname` varchar(512),
  `history_id` bigint(20) unsigned,
  `int_data` int(11),
  `text_data` varchar(1024),
  `obj_data` longblob,
  `ref_data` bigint(20) unsigned,
  `position_a_data` int(11),
  `position_b_data` int(11),
  `taxonomy_data` bigint(20) unsigned,
  `url_data` varchar(2048),
  `bool_data` tinyint(1),
  `taxonomy_name` varchar(512),
  `sequence_name` varchar(255)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_sequence_info`
--

DROP TABLE IF EXISTS `label_sequence_info`;
/*!50001 DROP VIEW IF EXISTS `label_sequence_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `label_sequence_info` (
  `label_id` bigint(20) unsigned,
  `id` bigint(20) unsigned,
  `seq_id` bigint(20) unsigned,
  `subname` varchar(512),
  `history_id` bigint(20) unsigned,
  `int_data` int(11),
  `text_data` varchar(1024),
  `obj_data` longblob,
  `ref_data` bigint(20) unsigned,
  `position_a_data` int(11),
  `position_b_data` int(11),
  `taxonomy_data` bigint(20) unsigned,
  `url_data` varchar(2048),
  `bool_data` tinyint(1),
  `taxonomy_name` varchar(512),
  `sequence_name` varchar(255),
  `type` enum('integer','text','obj','position','ref','tax','url','bool'),
  `name` char(255),
  `autoadd` tinyint(1),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` text,
  `deletable` tinyint(1),
  `editable` tinyint(1),
  `multiple` tinyint(1),
  `creation` timestamp,
  `creation_user_id` bigint(20) unsigned,
  `update` timestamp,
  `update_user_id` bigint(20) unsigned,
  `user_name` char(32)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sequence`
--

DROP TABLE IF EXISTS `sequence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sequence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `content` text CHARACTER SET ascii NOT NULL COMMENT 'Sequence itself.',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Sequence name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `sequence_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=415 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sequences table.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `sequence_info_history`
--

DROP TABLE IF EXISTS `sequence_info_history`;
/*!50001 DROP VIEW IF EXISTS `sequence_info_history`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `sequence_info_history` (
  `history_id` bigint(20) unsigned,
  `id` bigint(20) unsigned,
  `content` text,
  `name` varchar(255),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy`
--

DROP TABLE IF EXISTS `taxonomy`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `parent_id` int(11) DEFAULT NULL COMMENT 'Parent taxonomy.',
  `rank_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Rank.',
  `tree_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Tree ID.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `import_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Import ID.',
  `import_parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Import parent ID.',
  PRIMARY KEY (`id`),
  KEY `rank_index` (`rank_id`),
  KEY `history_id` (`history_id`),
  KEY `tree_id` (`tree_id`),
  CONSTRAINT `taxonomy_ibfk_2` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_ibfk_5` FOREIGN KEY (`rank_id`) REFERENCES `taxonomy_rank` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxonomy_ibfk_6` FOREIGN KEY (`tree_id`) REFERENCES `taxonomy_tree` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=784610 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy table.';
SET character_set_client = @saved_cs_client;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `drop_history_taxonomy` AFTER DELETE ON `taxonomy` FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `taxonomy_info`
--

DROP TABLE IF EXISTS `taxonomy_info`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_info` (
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `rank_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned,
  `parent_id` int(11),
  `rank_name` char(128),
  `tree_name` varchar(255),
  `import_id` bigint(20) unsigned,
  `import_parent_id` bigint(20) unsigned,
  `history_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_info_history`
--

DROP TABLE IF EXISTS `taxonomy_info_history`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info_history`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_info_history` (
  `history_id` bigint(20) unsigned,
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `rank_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned,
  `parent_id` int(11),
  `import_id` bigint(20) unsigned,
  `import_parent_id` bigint(20) unsigned,
  `rank_name` char(128),
  `tree_name` varchar(255),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy_name`
--

DROP TABLE IF EXISTS `taxonomy_name`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_name` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `tax_id` bigint(20) unsigned NOT NULL COMMENT 'Taxonomy ID.',
  `type_id` bigint(20) unsigned NOT NULL COMMENT 'Name type ID.',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `tax_id` (`tax_id`),
  CONSTRAINT `taxonomy_name_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `taxonomy` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_name_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `taxonomy_name_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=364229 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Taxonomy names.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_helper`
--

DROP TABLE IF EXISTS `taxonomy_name_helper`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_helper`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_name_helper` (
  `tax_id` bigint(20) unsigned,
  `rank_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_info`
--

DROP TABLE IF EXISTS `taxonomy_name_info`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_name_info` (
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `tax_id` bigint(20) unsigned,
  `type_id` bigint(20) unsigned,
  `type_name` varchar(512)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_tax`
--

DROP TABLE IF EXISTS `taxonomy_name_tax`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_tax`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_name_tax` (
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `tax_id` bigint(20) unsigned,
  `rank_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy_name_type`
--

DROP TABLE IF EXISTS `taxonomy_name_type`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_name_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Types of names for taxonomies.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_type_norm`
--

DROP TABLE IF EXISTS `taxonomy_name_type_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_type_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_name_type_norm` (
  `type_id` bigint(20) unsigned,
  `type_name` varchar(512)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy_rank`
--

DROP TABLE IF EXISTS `taxonomy_rank`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_rank` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` char(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Rank name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent rank.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `parent_id` (`parent_id`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `taxonomy_rank_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxonomy_rank_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `taxonomy_rank` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy ranks.';
SET character_set_client = @saved_cs_client;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `drop_history_rank` AFTER DELETE ON `taxonomy_rank` FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `taxonomy_rank_info`
--

DROP TABLE IF EXISTS `taxonomy_rank_info`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_rank_info` (
  `rank_id` bigint(20) unsigned,
  `rank_name` char(128),
  `rank_parent_id` bigint(20) unsigned,
  `rank_parent_name` char(128),
  `history_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_rank_info_history`
--

DROP TABLE IF EXISTS `taxonomy_rank_info_history`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info_history`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_rank_info_history` (
  `rank_id` bigint(20) unsigned,
  `rank_name` char(128),
  `rank_parent_id` bigint(20) unsigned,
  `rank_parent_name` char(128),
  `history_id` bigint(20) unsigned,
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_rank_norm`
--

DROP TABLE IF EXISTS `taxonomy_rank_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_rank_norm` (
  `rank_id` bigint(20) unsigned,
  `rank_name` char(128),
  `rank_parent_id` bigint(20) unsigned,
  `history_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_rank_parent_norm`
--

DROP TABLE IF EXISTS `taxonomy_rank_parent_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_parent_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_rank_parent_norm` (
  `rank_parent_name` char(128),
  `rank_parent_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy_tree`
--

DROP TABLE IF EXISTS `taxonomy_tree`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_tree` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tree name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Data history.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `history_id` (`history_id`),
  CONSTRAINT `taxonomy_tree_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_tree_info_history`
--

DROP TABLE IF EXISTS `taxonomy_tree_info_history`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_info_history`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_tree_info_history` (
  `history_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned,
  `tree_name` varchar(255),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_tree_norm`
--

DROP TABLE IF EXISTS `taxonomy_tree_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `taxonomy_tree_norm` (
  `tree_id` bigint(20) unsigned,
  `tree_name` varchar(255),
  `history_id` bigint(20) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User id.',
  `name` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User name.',
  `complete_name` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User full name.',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User creation time.',
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User password md5 hashed.',
  `email` varchar(129) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User email.',
  `user_type` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'User type.',
  `birthday` date DEFAULT NULL COMMENT 'User''s birthday.',
  `image` blob COMMENT 'User''s image file.',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Enable/Disable user.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User''s table.';
SET character_set_client = @saved_cs_client;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `user_md5_password_insert` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.password = MD5( NEW.password ) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`fdb_app`@`localhost`*/ /*!50003 TRIGGER `user_md5_password_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
  IF NEW.password <> OLD.password THEN
    SET NEW.password = MD5( NEW.password );
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `user_norm`
--

DROP TABLE IF EXISTS `user_norm`;
/*!50001 DROP VIEW IF EXISTS `user_norm`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `user_norm` (
  `user_id` bigint(20) unsigned,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_date` timestamp,
  `password` char(32),
  `email` varchar(129),
  `user_type` enum('user','admin'),
  `birthday` date,
  `image` blob,
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `history_info`
--

/*!50001 DROP TABLE `history_info`*/;
/*!50001 DROP VIEW IF EXISTS `history_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `history_info` AS select `history`.`id` AS `history_id`,`history`.`creation_user_id` AS `creation_user_id`,`history`.`creation` AS `creation`,`history`.`update_user_id` AS `update_user_id`,`history`.`update` AS `update`,`user_norm`.`user_name` AS `user_name`,`user_norm`.`complete_name` AS `complete_name`,`user_norm`.`creation_date` AS `creation_date`,`user_norm`.`password` AS `password`,`user_norm`.`email` AS `email`,`user_norm`.`user_type` AS `user_type`,`user_norm`.`birthday` AS `birthday`,`user_norm`.`image` AS `image`,`user_norm`.`enabled` AS `enabled` from (`history` join `user_norm` on((`history`.`update_user_id` = `user_norm`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_info_history`
--

/*!50001 DROP TABLE `label_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `label_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_info_history` AS select `label_norm`.`history_id` AS `history_id`,`label_norm`.`label_id` AS `label_id`,`label_norm`.`type` AS `type`,`label_norm`.`name` AS `name`,`label_norm`.`autoadd` AS `autoadd`,`label_norm`.`default` AS `default`,`label_norm`.`must_exist` AS `must_exist`,`label_norm`.`auto_on_creation` AS `auto_on_creation`,`label_norm`.`auto_on_modification` AS `auto_on_modification`,`label_norm`.`code` AS `code`,`label_norm`.`deletable` AS `deletable`,`label_norm`.`editable` AS `editable`,`label_norm`.`multiple` AS `multiple`,`label_norm`.`comment` AS `comment`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_date` AS `creation_date`,`history_info`.`password` AS `password`,`history_info`.`email` AS `email`,`history_info`.`user_type` AS `user_type`,`history_info`.`birthday` AS `birthday`,`history_info`.`image` AS `image`,`history_info`.`enabled` AS `enabled`,`label_norm`.`valid_code` AS `valid_code`,`label_norm`.`public` AS `public` from (`label_norm` left join `history_info` on((`label_norm`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_norm`
--

/*!50001 DROP TABLE `label_norm`*/;
/*!50001 DROP VIEW IF EXISTS `label_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_norm` AS select `label`.`id` AS `label_id`,`label`.`type` AS `type`,`label`.`name` AS `name`,`label`.`autoadd` AS `autoadd`,`label`.`default` AS `default`,`label`.`must_exist` AS `must_exist`,`label`.`auto_on_creation` AS `auto_on_creation`,`label`.`auto_on_modification` AS `auto_on_modification`,`label`.`code` AS `code`,`label`.`valid_code` AS `valid_code`,`label`.`deletable` AS `deletable`,`label`.`editable` AS `editable`,`label`.`multiple` AS `multiple`,`label`.`comment` AS `comment`,`label`.`history_id` AS `history_id`,`label`.`public` AS `public` from `label` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_sequence_extra`
--

/*!50001 DROP TABLE `label_sequence_extra`*/;
/*!50001 DROP VIEW IF EXISTS `label_sequence_extra`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_sequence_extra` AS select `label_sequence`.`id` AS `id`,`label_sequence`.`seq_id` AS `seq_id`,`label_sequence`.`label_id` AS `label_id`,`label_sequence`.`subname` AS `subname`,`label_sequence`.`history_id` AS `history_id`,`label_sequence`.`int_data` AS `int_data`,`label_sequence`.`text_data` AS `text_data`,`label_sequence`.`obj_data` AS `obj_data`,`label_sequence`.`ref_data` AS `ref_data`,`label_sequence`.`position_a_data` AS `position_a_data`,`label_sequence`.`position_b_data` AS `position_b_data`,`label_sequence`.`taxonomy_data` AS `taxonomy_data`,`label_sequence`.`url_data` AS `url_data`,`label_sequence`.`bool_data` AS `bool_data`,`taxonomy`.`name` AS `taxonomy_name`,`sequence`.`name` AS `sequence_name` from ((`label_sequence` left join `taxonomy` on((`taxonomy`.`id` = `label_sequence`.`taxonomy_data`))) left join `sequence` on((`sequence`.`id` = `label_sequence`.`ref_data`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_sequence_info`
--

/*!50001 DROP TABLE `label_sequence_info`*/;
/*!50001 DROP VIEW IF EXISTS `label_sequence_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_sequence_info` AS select `label_sequence_extra`.`label_id` AS `label_id`,`label_sequence_extra`.`id` AS `id`,`label_sequence_extra`.`seq_id` AS `seq_id`,`label_sequence_extra`.`subname` AS `subname`,`label_sequence_extra`.`history_id` AS `history_id`,`label_sequence_extra`.`int_data` AS `int_data`,`label_sequence_extra`.`text_data` AS `text_data`,`label_sequence_extra`.`obj_data` AS `obj_data`,`label_sequence_extra`.`ref_data` AS `ref_data`,`label_sequence_extra`.`position_a_data` AS `position_a_data`,`label_sequence_extra`.`position_b_data` AS `position_b_data`,`label_sequence_extra`.`taxonomy_data` AS `taxonomy_data`,`label_sequence_extra`.`url_data` AS `url_data`,`label_sequence_extra`.`bool_data` AS `bool_data`,`label_sequence_extra`.`taxonomy_name` AS `taxonomy_name`,`label_sequence_extra`.`sequence_name` AS `sequence_name`,`label_norm`.`type` AS `type`,`label_norm`.`name` AS `name`,`label_norm`.`autoadd` AS `autoadd`,`label_norm`.`default` AS `default`,`label_norm`.`must_exist` AS `must_exist`,`label_norm`.`auto_on_creation` AS `auto_on_creation`,`label_norm`.`auto_on_modification` AS `auto_on_modification`,`label_norm`.`code` AS `code`,`label_norm`.`deletable` AS `deletable`,`label_norm`.`editable` AS `editable`,`label_norm`.`multiple` AS `multiple`,`history_info`.`creation` AS `creation`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`update` AS `update`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`user_name` AS `user_name` from ((`label_sequence_extra` join `label_norm` on((`label_sequence_extra`.`label_id` = `label_norm`.`label_id`))) left join `history_info` on((`history_info`.`history_id` = `label_sequence_extra`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sequence_info_history`
--

/*!50001 DROP TABLE `sequence_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `sequence_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sequence_info_history` AS select `sequence`.`history_id` AS `history_id`,`sequence`.`id` AS `id`,`sequence`.`content` AS `content`,`sequence`.`name` AS `name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_date` AS `creation_date`,`history_info`.`password` AS `password`,`history_info`.`email` AS `email`,`history_info`.`user_type` AS `user_type`,`history_info`.`birthday` AS `birthday`,`history_info`.`image` AS `image`,`history_info`.`enabled` AS `enabled` from (`sequence` join `history_info` on((`sequence`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_info`
--

/*!50001 DROP TABLE `taxonomy_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_info` AS select `taxonomy`.`id` AS `id`,`taxonomy`.`name` AS `name`,`taxonomy`.`rank_id` AS `rank_id`,`taxonomy`.`tree_id` AS `tree_id`,`taxonomy`.`parent_id` AS `parent_id`,`taxonomy_rank_norm`.`rank_name` AS `rank_name`,`taxonomy_tree_norm`.`tree_name` AS `tree_name`,`taxonomy`.`import_id` AS `import_id`,`taxonomy`.`import_parent_id` AS `import_parent_id`,`taxonomy`.`history_id` AS `history_id` from ((`taxonomy` left join `taxonomy_rank_norm` on((`taxonomy`.`rank_id` = `taxonomy_rank_norm`.`rank_id`))) left join `taxonomy_tree_norm` on((`taxonomy`.`tree_id` = `taxonomy_tree_norm`.`tree_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_info_history`
--

/*!50001 DROP TABLE `taxonomy_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_info_history` AS select `taxonomy_info`.`history_id` AS `history_id`,`taxonomy_info`.`id` AS `id`,`taxonomy_info`.`name` AS `name`,`taxonomy_info`.`rank_id` AS `rank_id`,`taxonomy_info`.`tree_id` AS `tree_id`,`taxonomy_info`.`parent_id` AS `parent_id`,`taxonomy_info`.`import_id` AS `import_id`,`taxonomy_info`.`import_parent_id` AS `import_parent_id`,`taxonomy_info`.`rank_name` AS `rank_name`,`taxonomy_info`.`tree_name` AS `tree_name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_date` AS `creation_date`,`history_info`.`password` AS `password`,`history_info`.`email` AS `email`,`history_info`.`user_type` AS `user_type`,`history_info`.`birthday` AS `birthday`,`history_info`.`image` AS `image`,`history_info`.`enabled` AS `enabled` from (`taxonomy_info` left join `history_info` on((`taxonomy_info`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_helper`
--

/*!50001 DROP TABLE `taxonomy_name_helper`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_helper`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_helper` AS select `taxonomy`.`id` AS `tax_id`,`taxonomy`.`rank_id` AS `rank_id`,`taxonomy`.`tree_id` AS `tree_id` from `taxonomy` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_info`
--

/*!50001 DROP TABLE `taxonomy_name_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_info` AS select `taxonomy_name`.`id` AS `id`,`taxonomy_name`.`name` AS `name`,`taxonomy_name`.`tax_id` AS `tax_id`,`taxonomy_name`.`type_id` AS `type_id`,`taxonomy_name_type_norm`.`type_name` AS `type_name` from (`taxonomy_name` join `taxonomy_name_type_norm` on((`taxonomy_name`.`type_id` = `taxonomy_name_type_norm`.`type_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_tax`
--

/*!50001 DROP TABLE `taxonomy_name_tax`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_tax`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_tax` AS select `taxonomy_name`.`id` AS `id`,`taxonomy_name`.`name` AS `name`,`taxonomy_name`.`tax_id` AS `tax_id`,`taxonomy_name_helper`.`rank_id` AS `rank_id`,`taxonomy_name_helper`.`tree_id` AS `tree_id` from (`taxonomy_name` join `taxonomy_name_helper`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_type_norm`
--

/*!50001 DROP TABLE `taxonomy_name_type_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_type_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_type_norm` AS select `taxonomy_name_type`.`id` AS `type_id`,`taxonomy_name_type`.`name` AS `type_name` from `taxonomy_name_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_info`
--

/*!50001 DROP TABLE `taxonomy_rank_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_rank_info` AS select `taxonomy_rank_norm`.`rank_id` AS `rank_id`,`taxonomy_rank_norm`.`rank_name` AS `rank_name`,`taxonomy_rank_norm`.`rank_parent_id` AS `rank_parent_id`,`taxonomy_rank_parent_norm`.`rank_parent_name` AS `rank_parent_name`,`taxonomy_rank_norm`.`history_id` AS `history_id` from (`taxonomy_rank_norm` left join `taxonomy_rank_parent_norm` on((`taxonomy_rank_norm`.`rank_parent_id` = `taxonomy_rank_parent_norm`.`rank_parent_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_info_history`
--

/*!50001 DROP TABLE `taxonomy_rank_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_rank_info_history` AS select `taxonomy_rank_info`.`rank_id` AS `rank_id`,`taxonomy_rank_info`.`rank_name` AS `rank_name`,`taxonomy_rank_info`.`rank_parent_id` AS `rank_parent_id`,`taxonomy_rank_info`.`rank_parent_name` AS `rank_parent_name`,`history_info`.`history_id` AS `history_id`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_date` AS `creation_date`,`history_info`.`password` AS `password`,`history_info`.`email` AS `email`,`history_info`.`user_type` AS `user_type`,`history_info`.`birthday` AS `birthday`,`history_info`.`image` AS `image`,`history_info`.`enabled` AS `enabled` from (`taxonomy_rank_info` left join `history_info` on((`history_info`.`history_id` = `taxonomy_rank_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_norm`
--

/*!50001 DROP TABLE `taxonomy_rank_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_rank_norm` AS (select `taxonomy_rank`.`id` AS `rank_id`,`taxonomy_rank`.`name` AS `rank_name`,`taxonomy_rank`.`parent_id` AS `rank_parent_id`,`taxonomy_rank`.`history_id` AS `history_id` from `taxonomy_rank`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_parent_norm`
--

/*!50001 DROP TABLE `taxonomy_rank_parent_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_parent_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_rank_parent_norm` AS select `taxonomy_rank`.`name` AS `rank_parent_name`,`taxonomy_rank`.`id` AS `rank_parent_id` from `taxonomy_rank` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_tree_info_history`
--

/*!50001 DROP TABLE `taxonomy_tree_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_tree_info_history` AS select `taxonomy_tree_norm`.`history_id` AS `history_id`,`taxonomy_tree_norm`.`tree_id` AS `tree_id`,`taxonomy_tree_norm`.`tree_name` AS `tree_name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_date` AS `creation_date`,`history_info`.`password` AS `password`,`history_info`.`email` AS `email`,`history_info`.`user_type` AS `user_type`,`history_info`.`birthday` AS `birthday`,`history_info`.`image` AS `image`,`history_info`.`enabled` AS `enabled` from (`taxonomy_tree_norm` left join `history_info` on((`taxonomy_tree_norm`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_tree_norm`
--

/*!50001 DROP TABLE `taxonomy_tree_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_tree_norm` AS select `taxonomy_tree`.`id` AS `tree_id`,`taxonomy_tree`.`name` AS `tree_name`,`taxonomy_tree`.`history_id` AS `history_id` from `taxonomy_tree` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_norm`
--

/*!50001 DROP TABLE `user_norm`*/;
/*!50001 DROP VIEW IF EXISTS `user_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`fdb_app`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `user_norm` AS select `user`.`id` AS `user_id`,`user`.`name` AS `user_name`,`user`.`complete_name` AS `complete_name`,`user`.`creation_date` AS `creation_date`,`user`.`password` AS `password`,`user`.`email` AS `email`,`user`.`user_type` AS `user_type`,`user`.`birthday` AS `birthday`,`user`.`image` AS `image`,`user`.`enabled` AS `enabled` from `user` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-15 15:38:44
