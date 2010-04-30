-- MySQL dump 10.13  Distrib 5.1.41, for apple-darwin9.5.0 (i386)
--
-- Host: localhost    Database: biosed
-- ------------------------------------------------------
-- Server version	5.1.41

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key` char(128) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`key`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Event id.',
  `code` int(11) NOT NULL COMMENT 'Event code.',
  `data` text COMMENT 'Event data.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Event table.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID.',
  `label_id` bigint(20) unsigned DEFAULT '0' COMMENT 'Label for this file.',
  `name` varchar(512) NOT NULL COMMENT 'File name.',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of instances for this file.',
  `data` longblob NOT NULL COMMENT 'Data for the file.',
  `type` text COMMENT 'Optional file type.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_id` (`label_id`,`name`),
  KEY `label_index` (`label_id`),
  CONSTRAINT `file_ibfk_1` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for object label instances.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_file` BEFORE DELETE ON `file`
 FOR EACH ROW BEGIN
DELETE FROM label_sequence WHERE label_sequence.obj_data = OLD.id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='history';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `add_history_trigger` BEFORE INSERT ON `history` FOR EACH ROW BEGIN
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
/*!50003 CREATE*/ /*!50003 TRIGGER `update_history_trigger` BEFORE UPDATE ON `history` FOR EACH ROW BEGIN
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
  `enabled` tinyint(1),
  `creation_user_name` char(32),
  `creation_complete_name` varchar(512)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `type` enum('integer','float','text','obj','position','ref','tax','url','bool','date') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Label type.',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Label type name.',
  `comment` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Label type comment.',
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
  `action_modification` text COLLATE utf8_unicode_ci COMMENT 'Action to be run after a sequence modification.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  KEY `label_type` (`type`),
  CONSTRAINT `label_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label types.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER ins_label BEFORE INSERT ON label
FOR EACH ROW SET NEW.history_id = CREATE_HISTORY() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_label BEFORE UPDATE ON label FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
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
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_label` BEFORE DELETE ON `label`
 FOR EACH ROW
BEGIN
CALL DELETE_HISTORY(OLD.history_id);
DELETE FROM label_sequence WHERE label_sequence.label_id = OLD.id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
  `type` enum('integer','float','text','obj','position','ref','tax','url','bool','date'),
  `name` char(255),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` text,
  `deletable` tinyint(1),
  `editable` tinyint(1),
  `multiple` tinyint(1),
  `action_modification` text,
  `comment` varchar(1024),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
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
  `type` enum('integer','float','text','obj','position','ref','tax','url','bool','date'),
  `name` char(255),
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
  `public` tinyint(1),
  `action_modification` text
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `label_sequence`
--

DROP TABLE IF EXISTS `label_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label_sequence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `seq_id` bigint(20) unsigned NOT NULL COMMENT 'Sequence id.',
  `label_id` bigint(20) unsigned NOT NULL COMMENT 'Label id.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `int_data` int(11) DEFAULT NULL COMMENT 'Integer data.',
  `text_data` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Text data.',
  `ref_data` bigint(20) unsigned DEFAULT NULL COMMENT 'Reference Data.',
  `position_start` int(11) DEFAULT NULL COMMENT 'Position start.',
  `position_length` int(11) DEFAULT NULL COMMENT 'Position length.',
  `taxonomy_data` bigint(20) unsigned DEFAULT NULL COMMENT 'Taxonomy data.',
  `url_data` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL label data.',
  `bool_data` tinyint(1) DEFAULT NULL COMMENT 'Boolean data.',
  `date_data` datetime DEFAULT NULL COMMENT 'Data for date labels.',
  `float_data` double DEFAULT NULL COMMENT 'Float data.',
  `param` text COLLATE utf8_unicode_ci COMMENT 'Label instance parameter.',
  `obj_data` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`),
  KEY `label_id` (`label_id`),
  KEY `seq_id` (`seq_id`),
  KEY `ref_index` (`ref_data`),
  KEY `taxonomy_index` (`taxonomy_data`),
  KEY `obj_index` (`obj_data`),
  KEY `int_index` (`int_data`),
  KEY `float_index` (`float_data`),
  KEY `seq_label_index` (`seq_id`,`label_id`),
  KEY `text_data_index` (`text_data`(16))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label''s of sequences.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_label_seq BEFORE UPDATE ON label_sequence FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_after_label_seq AFTER UPDATE ON label_sequence FOR EACH ROW BEGIN IF NEW.obj_data IS NOT NULL AND NEW.obj_data <> OLD.obj_data THEN CALL decrement_file_ref(OLD.obj_data); CALL update_file_ref(NEW.obj_data); END IF; CALL UPDATE_HISTORY(NEW.history_id); END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER drop_label_seq AFTER DELETE ON label_sequence FOR EACH ROW begin CALL DELETE_HISTORY(OLD.history_id); IF OLD.obj_data IS NOT NULL THEN CALL decrement_file_ref(OLD.obj_data); END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `label_sequence_extra`
--

DROP TABLE IF EXISTS `label_sequence_extra`;
/*!50001 DROP VIEW IF EXISTS `label_sequence_extra`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `label_sequence_extra` (
  `id` bigint(20) unsigned,
  `param` text,
  `seq_id` bigint(20) unsigned,
  `label_id` bigint(20) unsigned,
  `history_id` bigint(20) unsigned,
  `int_data` int(11),
  `float_data` double,
  `text_data` varchar(1024),
  `obj_data` bigint(20) unsigned,
  `ref_data` bigint(20) unsigned,
  `position_start` int(11),
  `position_length` int(11),
  `taxonomy_data` bigint(20) unsigned,
  `url_data` varchar(2048),
  `bool_data` tinyint(1),
  `date_data` datetime,
  `taxonomy_name` varchar(512),
  `sequence_name` char(255),
  `file_name` varchar(512)
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
  `param` text,
  `seq_id` bigint(20) unsigned,
  `history_id` bigint(20) unsigned,
  `int_data` int(11),
  `float_data` double,
  `text_data` varchar(1024),
  `obj_data` bigint(20) unsigned,
  `ref_data` bigint(20) unsigned,
  `position_start` int(11),
  `position_length` int(11),
  `taxonomy_data` bigint(20) unsigned,
  `url_data` varchar(2048),
  `bool_data` tinyint(1),
  `date_data` datetime,
  `taxonomy_name` varchar(512),
  `sequence_name` char(255),
  `file_name` varchar(512),
  `type` enum('integer','float','text','obj','position','ref','tax','url','bool','date'),
  `name` char(255),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` text,
  `deletable` tinyint(1),
  `editable` tinyint(1),
  `multiple` tinyint(1),
  `public` tinyint(1),
  `action_modification` text,
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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `content` mediumtext CHARACTER SET ascii NOT NULL COMMENT 'Sequence itself.',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Sequence name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`),
  KEY `name` (`name`(16)),
  KEY `content` (`content`(16))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sequences table.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER ins_seq BEFORE INSERT ON sequence FOR EACH ROW SET NEW.history_id = CREATE_HISTORY() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_seq BEFORE UPDATE ON sequence FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
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
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_seq` BEFORE DELETE ON `sequence`
 FOR EACH ROW
BEGIN
CALL DELETE_HISTORY(OLD.history_id);
DELETE FROM label_sequence WHERE label_sequence.seq_id = OLD.id OR label_sequence.ref_data = OLD.id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
  `content` mediumtext,
  `name` char(255),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512),
  `creation_complete_name` varchar(512),
  `creation_user_name` char(32)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy`
--

DROP TABLE IF EXISTS `taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonomy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent taxonomy.',
  `rank_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Rank.',
  `tree_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Tree ID.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `import_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Import ID.',
  `import_parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Import parent ID.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `import_id` (`import_id`),
  KEY `rank_index` (`rank_id`),
  KEY `history_id` (`history_id`),
  KEY `tree_id` (`tree_id`),
  KEY `parent_id` (`parent_id`),
  KEY `import_parent_id` (`import_parent_id`),
  KEY `taxonomy_name` (`name`(16)),
  CONSTRAINT `taxonomy_ibfk_10` FOREIGN KEY (`parent_id`) REFERENCES `taxonomy` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_ibfk_7` FOREIGN KEY (`rank_id`) REFERENCES `taxonomy_rank` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_ibfk_8` FOREIGN KEY (`tree_id`) REFERENCES `taxonomy_tree` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_ibfk_9` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy table.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER ins_tax BEFORE INSERT ON taxonomy FOR EACH ROW SET NEW.history_id = CREATE_HISTORY() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_tax BEFORE UPDATE ON taxonomy FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
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
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_tax` BEFORE DELETE ON `taxonomy`
 FOR EACH ROW
BEGIN
   CALL DELETE_HISTORY(OLD.history_id);
   DELETE FROM label_sequence WHERE label_sequence.taxonomy_data = OLD.id;
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
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_history_taxonomy` AFTER DELETE ON `taxonomy` FOR EACH ROW BEGIN
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
  `parent_id` bigint(20) unsigned,
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
  `parent_id` bigint(20) unsigned,
  `import_id` bigint(20) unsigned,
  `import_parent_id` bigint(20) unsigned,
  `rank_name` char(128),
  `tree_name` varchar(255),
  `creation_user_id` bigint(20) unsigned,
  `creation` timestamp,
  `update_user_id` bigint(20) unsigned,
  `update` timestamp,
  `user_name` char(32),
  `complete_name` varchar(512)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy_name`
--

DROP TABLE IF EXISTS `taxonomy_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Taxonomy names.';
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `taxonomy_name_type`
--

DROP TABLE IF EXISTS `taxonomy_name_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonomy_name_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name.',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Types of names for taxonomies.';
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonomy_rank` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key.',
  `name` char(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Rank name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'History.',
  `parent_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent rank.',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is this a default rank?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `taxonomy_rank_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxonomy_rank_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `taxonomy_rank` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy ranks.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER ins_tax_rank BEFORE INSERT ON taxonomy_rank FOR EACH ROW SET NEW.history_id = CREATE_HISTORY() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_tax_rank BEFORE UPDATE ON taxonomy_rank FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER drop_tax_rank BEFORE DELETE ON taxonomy_rank FOR EACH ROW CALL DELETE_HISTORY(OLD.history_id) */;;
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
/*!50003 CREATE*/ /*!50003 TRIGGER `drop_history_rank` AFTER DELETE ON `taxonomy_rank` FOR EACH ROW BEGIN
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
  `complete_name` varchar(512)
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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonomy_tree` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key.',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tree name.',
  `history_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Data history.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `history_id` (`history_id`),
  CONSTRAINT `taxonomy_tree_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER ins_tax_tree BEFORE INSERT ON taxonomy_tree FOR EACH ROW SET NEW.history_id = CREATE_HISTORY() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER upd_tax_tree BEFORE UPDATE ON taxonomy_tree FOR EACH ROW BEGIN IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER drop_tax_tree BEFORE DELETE ON taxonomy_tree FOR EACH ROW CALL DELETE_HISTORY(OLD.history_id) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
  `complete_name` varchar(512)
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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User id.',
  `name` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User name.',
  `complete_name` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User full name.',
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User password md5 hashed.',
  `email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User email.',
  `user_type` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'User type.',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Enable/Disable user.',
  `history_id` bigint(20) unsigned DEFAULT NULL,
  `last_access` datetime DEFAULT NULL COMMENT 'Date/Time of last access for this user.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User''s table.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `ins_user` BEFORE INSERT ON `user`
 FOR EACH ROW BEGIN SET NEW.history_id = CREATE_HISTORY(); SET NEW.password = MD5(CONCAT("xrg82bAcEFg4wVy02VLIPJncBMhPg0ievL2k4WOhQI1jC4vXBjwb2MMRWabP1anwATdsjDaxGHFL1TYhOTFT7g78GxrGgn2fC9vc", NEW.password)); END */;;
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
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `upd_user` BEFORE UPDATE ON `user`
 FOR EACH ROW BEGIN IF NEW.password <> OLD.password THEN SET NEW.password = MD5(CONCAT("xrg82bAcEFg4wVy02VLIPJncBMhPg0ievL2k4WOhQI1jC4vXBjwb2MMRWabP1anwATdsjDaxGHFL1TYhOTFT7g78GxrGgn2fC9vc", NEW.password)); END IF; IF NEW.history_id IS NULL THEN SET NEW.history_id = CREATE_HISTORY(); ELSE CALL UPDATE_HISTORY(OLD.history_id); END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER drop_user AFTER DELETE ON user FOR EACH ROW BEGIN CALL DELETE_HISTORY(OLD.history_id); DELETE FROM configuration WHERE user_id = OLD.id; END */;;
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
  `password` char(32),
  `email` varchar(128),
  `user_type` enum('user','admin'),
  `enabled` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `user_norm_creation`
--

DROP TABLE IF EXISTS `user_norm_creation`;
/*!50001 DROP VIEW IF EXISTS `user_norm_creation`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `user_norm_creation` (
  `creation_user_id` bigint(20) unsigned,
  `creation_user_name` char(32),
  `creation_complete_name` varchar(512)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'biosed'
--
/*!50003 DROP FUNCTION IF EXISTS `CREATE_HISTORY` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 FUNCTION `CREATE_HISTORY`() RETURNS bigint(20)
BEGIN INSERT INTO history() VALUES(); RETURN LAST_INSERT_ID(); END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `label_sequences` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 FUNCTION `label_sequences`(id INT) RETURNS int(11)
BEGIN DECLARE ret INT; SELECT COUNT(DISTINCT seq_id) INTO ret FROM label_sequence WHERE label_sequence.label_id = id; RETURN ret; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `total_sequences` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 FUNCTION `total_sequences`() RETURNS int(11)
BEGIN DECLARE ret INT; SELECT COUNT(id) INTO ret FROM sequence; RETURN ret; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY`()
BEGIN
	CALL CHECK_HISTORY_LABEL();
	CALL CHECK_HISTORY_LABEL_SEQUENCE();
	CALL CHECK_HISTORY_SEQUENCE();
	CALL CHECK_HISTORY_TAXONOMY();
	CALL CHECK_HISTORY_TAXONOMY_RANK();
	CALL CHECK_HISTORY_TAXONOMY_TREE();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_LABEL` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_LABEL`()
BEGIN
	UPDATE label SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_LABEL_SEQUENCE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_LABEL_SEQUENCE`()
BEGIN
	UPDATE label_sequence SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_SEQUENCE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_SEQUENCE`()
BEGIN
	UPDATE sequence SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_TAXONOMY` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_TAXONOMY`()
BEGIN
	UPDATE taxonomy SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_TAXONOMY_RANK` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_TAXONOMY_RANK`()
BEGIN
	UPDATE taxonomy_rank SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CHECK_HISTORY_TAXONOMY_TREE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `CHECK_HISTORY_TAXONOMY_TREE`()
BEGIN
	UPDATE taxonomy_tree SET history_id = CREATE_HISTORY() WHERE history_id IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `decrement_file_ref` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `decrement_file_ref`(IN file_id bigint(20))
BEGIN
   DECLARE current INT DEFAULT 0;

   SELECT `count` INTO current
   FROM file
   WHERE id = file_id;

   SET current = current - 1;

   IF current = 0 THEN
     DELETE FROM file WHERE id = file_id;
   ELSE
     UPDATE file SET `count` = current WHERE id = file_id;
   END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `DELETE_HISTORY` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `DELETE_HISTORY`(hid BIGINT(20))
BEGIN if hid IS NOT NULL THEN DELETE FROM history WHERE history.id = hid; END IF; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_file_ref` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `update_file_ref`(IN file_id bigint(20))
BEGIN
   DECLARE current INT DEFAULT 0;

   SELECT `count` INTO current
   FROM file
   WHERE id = file_id;

   SET current = current + 1;

   UPDATE file SET `count` = current WHERE id = file_id;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UPDATE_HISTORY` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `UPDATE_HISTORY`(hid BIGINT(20))
BEGIN UPDATE history SET history.update = CURRENT_TIMESTAMP() WHERE history.id = hid; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `history_info`
--

/*!50001 DROP TABLE IF EXISTS `history_info`*/;
/*!50001 DROP VIEW IF EXISTS `history_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `history_info` AS select `history`.`id` AS `history_id`,`history`.`creation_user_id` AS `creation_user_id`,`history`.`creation` AS `creation`,`history`.`update_user_id` AS `update_user_id`,`history`.`update` AS `update`,`user_norm`.`user_name` AS `user_name`,`user_norm`.`complete_name` AS `complete_name`,`user_norm`.`enabled` AS `enabled`,`user_norm_creation`.`creation_user_name` AS `creation_user_name`,`user_norm_creation`.`creation_complete_name` AS `creation_complete_name` from ((`history` left join `user_norm` on((`history`.`update_user_id` = `user_norm`.`user_id`))) left join `user_norm_creation` on((`history`.`creation_user_id` = `user_norm_creation`.`creation_user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_info_history`
--

/*!50001 DROP TABLE IF EXISTS `label_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `label_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `label_info_history` AS select `label_norm`.`history_id` AS `history_id`,`label_norm`.`label_id` AS `label_id`,`label_norm`.`type` AS `type`,`label_norm`.`name` AS `name`,`label_norm`.`default` AS `default`,`label_norm`.`must_exist` AS `must_exist`,`label_norm`.`auto_on_creation` AS `auto_on_creation`,`label_norm`.`auto_on_modification` AS `auto_on_modification`,`label_norm`.`code` AS `code`,`label_norm`.`deletable` AS `deletable`,`label_norm`.`editable` AS `editable`,`label_norm`.`multiple` AS `multiple`,`label_norm`.`action_modification` AS `action_modification`,`label_norm`.`comment` AS `comment`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`label_norm`.`valid_code` AS `valid_code`,`label_norm`.`public` AS `public` from (`label_norm` left join `history_info` on((`label_norm`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_norm`
--

/*!50001 DROP TABLE IF EXISTS `label_norm`*/;
/*!50001 DROP VIEW IF EXISTS `label_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `label_norm` AS select `label`.`id` AS `label_id`,`label`.`type` AS `type`,`label`.`name` AS `name`,`label`.`default` AS `default`,`label`.`must_exist` AS `must_exist`,`label`.`auto_on_creation` AS `auto_on_creation`,`label`.`auto_on_modification` AS `auto_on_modification`,`label`.`code` AS `code`,`label`.`valid_code` AS `valid_code`,`label`.`deletable` AS `deletable`,`label`.`editable` AS `editable`,`label`.`multiple` AS `multiple`,`label`.`comment` AS `comment`,`label`.`history_id` AS `history_id`,`label`.`public` AS `public`,`label`.`action_modification` AS `action_modification` from `label` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_sequence_extra`
--

/*!50001 DROP TABLE IF EXISTS `label_sequence_extra`*/;
/*!50001 DROP VIEW IF EXISTS `label_sequence_extra`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `label_sequence_extra` AS select `label_sequence`.`id` AS `id`,`label_sequence`.`param` AS `param`,`label_sequence`.`seq_id` AS `seq_id`,`label_sequence`.`label_id` AS `label_id`,`label_sequence`.`history_id` AS `history_id`,`label_sequence`.`int_data` AS `int_data`,`label_sequence`.`float_data` AS `float_data`,`label_sequence`.`text_data` AS `text_data`,`label_sequence`.`obj_data` AS `obj_data`,`label_sequence`.`ref_data` AS `ref_data`,`label_sequence`.`position_start` AS `position_start`,`label_sequence`.`position_length` AS `position_length`,`label_sequence`.`taxonomy_data` AS `taxonomy_data`,`label_sequence`.`url_data` AS `url_data`,`label_sequence`.`bool_data` AS `bool_data`,`label_sequence`.`date_data` AS `date_data`,`taxonomy`.`name` AS `taxonomy_name`,`sequence`.`name` AS `sequence_name`,`file`.`name` AS `file_name` from (((`label_sequence` left join `taxonomy` on((`taxonomy`.`id` = `label_sequence`.`taxonomy_data`))) left join `sequence` on((`sequence`.`id` = `label_sequence`.`ref_data`))) left join `file` on((`file`.`id` = `label_sequence`.`obj_data`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `label_sequence_info`
--

/*!50001 DROP TABLE IF EXISTS `label_sequence_info`*/;
/*!50001 DROP VIEW IF EXISTS `label_sequence_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `label_sequence_info` AS select `label_sequence_extra`.`label_id` AS `label_id`,`label_sequence_extra`.`id` AS `id`,`label_sequence_extra`.`param` AS `param`,`label_sequence_extra`.`seq_id` AS `seq_id`,`label_sequence_extra`.`history_id` AS `history_id`,`label_sequence_extra`.`int_data` AS `int_data`,`label_sequence_extra`.`float_data` AS `float_data`,`label_sequence_extra`.`text_data` AS `text_data`,`label_sequence_extra`.`obj_data` AS `obj_data`,`label_sequence_extra`.`ref_data` AS `ref_data`,`label_sequence_extra`.`position_start` AS `position_start`,`label_sequence_extra`.`position_length` AS `position_length`,`label_sequence_extra`.`taxonomy_data` AS `taxonomy_data`,`label_sequence_extra`.`url_data` AS `url_data`,`label_sequence_extra`.`bool_data` AS `bool_data`,`label_sequence_extra`.`date_data` AS `date_data`,`label_sequence_extra`.`taxonomy_name` AS `taxonomy_name`,`label_sequence_extra`.`sequence_name` AS `sequence_name`,`label_sequence_extra`.`file_name` AS `file_name`,`label_norm`.`type` AS `type`,`label_norm`.`name` AS `name`,`label_norm`.`default` AS `default`,`label_norm`.`must_exist` AS `must_exist`,`label_norm`.`auto_on_creation` AS `auto_on_creation`,`label_norm`.`auto_on_modification` AS `auto_on_modification`,`label_norm`.`code` AS `code`,`label_norm`.`deletable` AS `deletable`,`label_norm`.`editable` AS `editable`,`label_norm`.`multiple` AS `multiple`,`label_norm`.`public` AS `public`,`label_norm`.`action_modification` AS `action_modification`,`history_info`.`creation` AS `creation`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`update` AS `update`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`user_name` AS `user_name` from ((`label_sequence_extra` join `label_norm` on((`label_sequence_extra`.`label_id` = `label_norm`.`label_id`))) left join `history_info` on((`history_info`.`history_id` = `label_sequence_extra`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sequence_info_history`
--

/*!50001 DROP TABLE IF EXISTS `sequence_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `sequence_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `sequence_info_history` AS select `sequence`.`history_id` AS `history_id`,`sequence`.`id` AS `id`,`sequence`.`content` AS `content`,`sequence`.`name` AS `name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name`,`history_info`.`creation_complete_name` AS `creation_complete_name`,`history_info`.`creation_user_name` AS `creation_user_name` from (`sequence` left join `history_info` on((`sequence`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_info`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_info` AS select `taxonomy`.`id` AS `id`,`taxonomy`.`name` AS `name`,`taxonomy`.`rank_id` AS `rank_id`,`taxonomy`.`tree_id` AS `tree_id`,`taxonomy`.`parent_id` AS `parent_id`,`taxonomy_rank_norm`.`rank_name` AS `rank_name`,`taxonomy_tree_norm`.`tree_name` AS `tree_name`,`taxonomy`.`import_id` AS `import_id`,`taxonomy`.`import_parent_id` AS `import_parent_id`,`taxonomy`.`history_id` AS `history_id` from ((`taxonomy` left join `taxonomy_rank_norm` on((`taxonomy`.`rank_id` = `taxonomy_rank_norm`.`rank_id`))) left join `taxonomy_tree_norm` on((`taxonomy`.`tree_id` = `taxonomy_tree_norm`.`tree_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_info_history`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_info_history` AS select `taxonomy_info`.`history_id` AS `history_id`,`taxonomy_info`.`id` AS `id`,`taxonomy_info`.`name` AS `name`,`taxonomy_info`.`rank_id` AS `rank_id`,`taxonomy_info`.`tree_id` AS `tree_id`,`taxonomy_info`.`parent_id` AS `parent_id`,`taxonomy_info`.`import_id` AS `import_id`,`taxonomy_info`.`import_parent_id` AS `import_parent_id`,`taxonomy_info`.`rank_name` AS `rank_name`,`taxonomy_info`.`tree_name` AS `tree_name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name` from (`taxonomy_info` left join `history_info` on((`taxonomy_info`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_info`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_name_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_name_info` AS select `taxonomy_name`.`id` AS `id`,`taxonomy_name`.`name` AS `name`,`taxonomy_name`.`tax_id` AS `tax_id`,`taxonomy_name`.`type_id` AS `type_id`,`taxonomy_name_type_norm`.`type_name` AS `type_name` from (`taxonomy_name` join `taxonomy_name_type_norm` on((`taxonomy_name`.`type_id` = `taxonomy_name_type_norm`.`type_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_name_type_norm`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_name_type_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_type_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_name_type_norm` AS select `taxonomy_name_type`.`id` AS `type_id`,`taxonomy_name_type`.`name` AS `type_name` from `taxonomy_name_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_info`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_rank_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_rank_info` AS select `taxonomy_rank_norm`.`rank_id` AS `rank_id`,`taxonomy_rank_norm`.`rank_name` AS `rank_name`,`taxonomy_rank_norm`.`rank_parent_id` AS `rank_parent_id`,`taxonomy_rank_parent_norm`.`rank_parent_name` AS `rank_parent_name`,`taxonomy_rank_norm`.`history_id` AS `history_id` from (`taxonomy_rank_norm` left join `taxonomy_rank_parent_norm` on((`taxonomy_rank_norm`.`rank_parent_id` = `taxonomy_rank_parent_norm`.`rank_parent_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_info_history`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_rank_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_rank_info_history` AS select `taxonomy_rank_info`.`rank_id` AS `rank_id`,`taxonomy_rank_info`.`rank_name` AS `rank_name`,`taxonomy_rank_info`.`rank_parent_id` AS `rank_parent_id`,`taxonomy_rank_info`.`rank_parent_name` AS `rank_parent_name`,`history_info`.`history_id` AS `history_id`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name` from (`taxonomy_rank_info` left join `history_info` on((`history_info`.`history_id` = `taxonomy_rank_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_norm`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_rank_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_rank_norm` AS (select `taxonomy_rank`.`id` AS `rank_id`,`taxonomy_rank`.`name` AS `rank_name`,`taxonomy_rank`.`parent_id` AS `rank_parent_id`,`taxonomy_rank`.`history_id` AS `history_id` from `taxonomy_rank`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_rank_parent_norm`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_rank_parent_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_parent_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_rank_parent_norm` AS select `taxonomy_rank`.`name` AS `rank_parent_name`,`taxonomy_rank`.`id` AS `rank_parent_id` from `taxonomy_rank` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_tree_info_history`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_tree_info_history`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_info_history`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_tree_info_history` AS select `taxonomy_tree_norm`.`history_id` AS `history_id`,`taxonomy_tree_norm`.`tree_id` AS `tree_id`,`taxonomy_tree_norm`.`tree_name` AS `tree_name`,`history_info`.`creation_user_id` AS `creation_user_id`,`history_info`.`creation` AS `creation`,`history_info`.`update_user_id` AS `update_user_id`,`history_info`.`update` AS `update`,`history_info`.`user_name` AS `user_name`,`history_info`.`complete_name` AS `complete_name` from (`taxonomy_tree_norm` left join `history_info` on((`taxonomy_tree_norm`.`history_id` = `history_info`.`history_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `taxonomy_tree_norm`
--

/*!50001 DROP TABLE IF EXISTS `taxonomy_tree_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `taxonomy_tree_norm` AS select `taxonomy_tree`.`id` AS `tree_id`,`taxonomy_tree`.`name` AS `tree_name`,`taxonomy_tree`.`history_id` AS `history_id` from `taxonomy_tree` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_norm`
--

/*!50001 DROP TABLE IF EXISTS `user_norm`*/;
/*!50001 DROP VIEW IF EXISTS `user_norm`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `user_norm` AS select `user`.`id` AS `user_id`,`user`.`name` AS `user_name`,`user`.`complete_name` AS `complete_name`,`user`.`password` AS `password`,`user`.`email` AS `email`,`user`.`user_type` AS `user_type`,`user`.`enabled` AS `enabled` from `user` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_norm_creation`
--

/*!50001 DROP TABLE IF EXISTS `user_norm_creation`*/;
/*!50001 DROP VIEW IF EXISTS `user_norm_creation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `user_norm_creation` AS select `user`.`id` AS `creation_user_id`,`user`.`name` AS `creation_user_name`,`user`.`complete_name` AS `creation_complete_name` from `user` */;
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

-- Dump completed on 2010-04-30 15:48:18
