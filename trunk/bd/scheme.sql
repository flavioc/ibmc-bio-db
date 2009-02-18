-- MySQL dump 10.11
--
-- Host: localhost    Database: FDB
-- ------------------------------------------------------
-- Server version	5.0.75

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
  `key` char(128) collate utf8_unicode_ci NOT NULL COMMENT 'Configuration Key.',
  `value` varchar(512) collate utf8_unicode_ci default NULL COMMENT 'Configuration value.',
  PRIMARY KEY  (`user_id`,`key`),
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
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `creation_user_id` bigint(20) unsigned default NULL COMMENT 'User that created this.',
  `creation` timestamp NULL default CURRENT_TIMESTAMP COMMENT 'Date/Time of creation.',
  `update_user_id` bigint(20) unsigned default NULL COMMENT 'User that modified this.',
  `update` timestamp NULL default NULL COMMENT 'Date/Time of last modification.',
  PRIMARY KEY  (`id`),
  KEY `creation_user_id` (`creation_user_id`),
  KEY `update_user_id` (`update_user_id`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`creation_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `history_ibfk_2` FOREIGN KEY (`update_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='history';
SET character_set_client = @saved_cs_client;

/*!50003 SET @SAVE_SQL_MODE=@@SQL_MODE*/;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `add_history_trigger` BEFORE INSERT ON `history` FOR EACH ROW BEGIN
    SET NEW.`update` = NOW();
    SET NEW.`creation_user_id` = NEW.`update_user_id`;
  END */;;

/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `update_history_trigger` BEFORE UPDATE ON `history` FOR EACH ROW BEGIN
    set @author = OLD.creation_user_id;
    IF (@author IS NULL) THEN
      SET NEW.creation_user_id = NEW.update_user_id;
    END IF;
    SET NEW.`update` = NOW();
  END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@SAVE_SQL_MODE*/;

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `label` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `type` enum('integer','text','obj','position','ref','tax','url') collate utf8_unicode_ci NOT NULL COMMENT 'Label type.',
  `name` char(255) collate utf8_unicode_ci NOT NULL COMMENT 'Label type name.',
  `comment` varchar(1024) collate utf8_unicode_ci default NULL COMMENT 'Label type comment.',
  `autoadd` tinyint(1) NOT NULL default '0' COMMENT 'Auto add to new sequences?',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  `default` tinyint(1) NOT NULL default '0' COMMENT 'Is this a default label?',
  `must_exist` tinyint(1) NOT NULL COMMENT 'If this label must exist in all sequences.',
  `auto_on_creation` tinyint(1) NOT NULL COMMENT 'Generate label on creation.',
  `auto_on_modification` tinyint(1) NOT NULL COMMENT 'Generate label on modification.',
  `code` varchar(16384) collate utf8_unicode_ci default NULL COMMENT 'Code to generate label.',
  `deletable` tinyint(1) NOT NULL default '0' COMMENT 'Can this label be deleted?',
  `editable` tinyint(1) NOT NULL default '1' COMMENT 'If true label sequence data can be edited.',
  `multiple` tinyint(1) NOT NULL default '1' COMMENT 'If the label can be used multiple times per sequence.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `label_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label types.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_norm`
--

DROP TABLE IF EXISTS `label_norm`;
/*!50001 DROP VIEW IF EXISTS `label_norm`*/;
/*!50001 CREATE TABLE `label_norm` (
  `label_id` bigint(20) unsigned,
  `type` enum('integer','text','obj','position','ref','tax','url'),
  `name` char(255),
  `autoadd` tinyint(1),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` varchar(16384),
  `deletable` tinyint(1),
  `editable` tinyint(1)
) ENGINE=MyISAM */;

--
-- Table structure for table `label_sequence`
--

DROP TABLE IF EXISTS `label_sequence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `label_sequence` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `seq_id` bigint(20) unsigned NOT NULL COMMENT 'Sequence id.',
  `label_id` bigint(20) unsigned NOT NULL COMMENT 'Label id.',
  `subname` varchar(512) collate utf8_unicode_ci default NULL COMMENT 'Sub name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  `int_data` int(11) default NULL COMMENT 'Integer data.',
  `text_data` varchar(1024) collate utf8_unicode_ci default NULL COMMENT 'Text data.',
  `obj_data` blob COMMENT 'Object data.',
  `ref_data` bigint(20) unsigned default NULL COMMENT 'Reference Data.',
  `position_a_data` int(11) default NULL COMMENT 'Position data A.',
  `position_b_data` int(11) default NULL COMMENT 'Position data B.',
  `taxonomy_data` bigint(20) unsigned default NULL COMMENT 'Taxonomy data.',
  `url_data` varchar(2048) collate utf8_unicode_ci default NULL COMMENT 'URL label data.',
  PRIMARY KEY  (`id`),
  KEY `history_id` (`history_id`),
  KEY `label_id` (`label_id`),
  KEY `seq_id` (`seq_id`),
  KEY `ref_index` (`ref_data`),
  KEY `taxonomy_index` (`taxonomy_data`),
  CONSTRAINT `label_sequence_ibfk_1` FOREIGN KEY (`seq_id`) REFERENCES `sequence` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_sequence_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_sequence_ibfk_3` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL,
  CONSTRAINT `label_sequence_ibfk_4` FOREIGN KEY (`ref_data`) REFERENCES `sequence` (`id`),
  CONSTRAINT `label_sequence_ibfk_5` FOREIGN KEY (`taxonomy_data`) REFERENCES `taxonomy` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label''s of sequences.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `label_sequence_info`
--

DROP TABLE IF EXISTS `label_sequence_info`;
/*!50001 DROP VIEW IF EXISTS `label_sequence_info`*/;
/*!50001 CREATE TABLE `label_sequence_info` (
  `label_id` bigint(20) unsigned,
  `id` bigint(20) unsigned,
  `seq_id` bigint(20) unsigned,
  `subname` varchar(512),
  `history_id` bigint(20) unsigned,
  `int_data` int(11),
  `text_data` varchar(1024),
  `obj_data` blob,
  `ref_data` bigint(20) unsigned,
  `position_a_data` int(11),
  `position_b_data` int(11),
  `taxonomy_data` bigint(20) unsigned,
  `url_data` varchar(2048),
  `type` enum('integer','text','obj','position','ref','tax','url'),
  `name` char(255),
  `autoadd` tinyint(1),
  `default` tinyint(1),
  `must_exist` tinyint(1),
  `auto_on_creation` tinyint(1),
  `auto_on_modification` tinyint(1),
  `code` varchar(16384),
  `deletable` tinyint(1),
  `editable` tinyint(1)
) ENGINE=MyISAM */;

--
-- Table structure for table `sequence`
--

DROP TABLE IF EXISTS `sequence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sequence` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `content` text character set ascii NOT NULL COMMENT 'Sequence itself.',
  `accession` char(255) collate utf8_unicode_ci default NULL COMMENT 'Accession number.',
  `type` enum('dna','protein') collate utf8_unicode_ci NOT NULL default 'dna' COMMENT 'Sequence type.',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Sequence name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  PRIMARY KEY  (`id`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `sequence_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sequences table.';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `taxonomy`
--

DROP TABLE IF EXISTS `taxonomy`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `parent_id` int(11) default NULL COMMENT 'Parent taxonomy.',
  `rank_id` bigint(20) unsigned NOT NULL COMMENT 'Rank.',
  `tree_id` bigint(20) unsigned default NULL COMMENT 'Tree ID.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  `import_id` bigint(20) unsigned default NULL COMMENT 'Import ID.',
  `import_parent_id` bigint(20) unsigned default NULL COMMENT 'Import parent ID.',
  PRIMARY KEY  (`id`),
  KEY `rank_index` (`rank_id`),
  KEY `history_id` (`history_id`),
  KEY `tree_id` (`tree_id`),
  CONSTRAINT `taxonomy_ibfk_2` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_ibfk_3` FOREIGN KEY (`rank_id`) REFERENCES `taxonomy_rank` (`id`),
  CONSTRAINT `taxonomy_ibfk_4` FOREIGN KEY (`tree_id`) REFERENCES `taxonomy_tree` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22957 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy table.';
SET character_set_client = @saved_cs_client;

/*!50003 SET @SAVE_SQL_MODE=@@SQL_MODE*/;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `drop_history_taxonomy` AFTER DELETE ON `taxonomy` FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@SAVE_SQL_MODE*/;

--
-- Temporary table structure for view `taxonomy_all_names`
--

DROP TABLE IF EXISTS `taxonomy_all_names`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_all_names`*/;
/*!50001 CREATE TABLE `taxonomy_all_names` (
  `id` bigint(20) unsigned,
  `name` varchar(512)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `taxonomy_info`
--

DROP TABLE IF EXISTS `taxonomy_info`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info`*/;
/*!50001 CREATE TABLE `taxonomy_info` (
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `rank_id` bigint(20) unsigned,
  `tree_id` bigint(20) unsigned,
  `parent_id` int(11),
  `parent_name` varchar(512),
  `rank_name` char(128),
  `tree_name` varchar(255)
) ENGINE=MyISAM */;

--
-- Table structure for table `taxonomy_name`
--

DROP TABLE IF EXISTS `taxonomy_name`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_name` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `tax_id` bigint(20) unsigned NOT NULL COMMENT 'Taxonomy ID.',
  `type_id` bigint(20) unsigned NOT NULL COMMENT 'Name type ID.',
  PRIMARY KEY  (`id`),
  KEY `type_id` (`type_id`),
  KEY `tax_id` (`tax_id`),
  CONSTRAINT `taxonomy_name_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `taxonomy` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxonomy_name_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `taxonomy_name_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27856 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Taxonomy names.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_info`
--

DROP TABLE IF EXISTS `taxonomy_name_info`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_info`*/;
/*!50001 CREATE TABLE `taxonomy_name_info` (
  `id` bigint(20) unsigned,
  `name` varchar(512),
  `tax_id` bigint(20) unsigned,
  `type_id` bigint(20) unsigned,
  `type_name` varchar(512)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `taxonomy_name_only_names`
--

DROP TABLE IF EXISTS `taxonomy_name_only_names`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_only_names`*/;
/*!50001 CREATE TABLE `taxonomy_name_only_names` (
  `id` bigint(20) unsigned,
  `name` varchar(512)
) ENGINE=MyISAM */;

--
-- Table structure for table `taxonomy_name_type`
--

DROP TABLE IF EXISTS `taxonomy_name_type`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_name_type` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Types of names for taxonomies.';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_name_type_norm`
--

DROP TABLE IF EXISTS `taxonomy_name_type_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_type_norm`*/;
/*!50001 CREATE TABLE `taxonomy_name_type_norm` (
  `type_id` bigint(20) unsigned,
  `type_name` varchar(512)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `taxonomy_only_names`
--

DROP TABLE IF EXISTS `taxonomy_only_names`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_only_names`*/;
/*!50001 CREATE TABLE `taxonomy_only_names` (
  `id` bigint(20) unsigned,
  `name` varchar(512)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `taxonomy_parent`
--

DROP TABLE IF EXISTS `taxonomy_parent`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_parent`*/;
/*!50001 CREATE TABLE `taxonomy_parent` (
  `parent` bigint(20) unsigned,
  `parent_name` varchar(512)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `taxonomy_parent_norm`
--

DROP TABLE IF EXISTS `taxonomy_parent_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_parent_norm`*/;
/*!50001 CREATE TABLE `taxonomy_parent_norm` (
  `parent_id` bigint(20) unsigned,
  `parent_name` varchar(512)
) ENGINE=MyISAM */;

--
-- Table structure for table `taxonomy_rank`
--

DROP TABLE IF EXISTS `taxonomy_rank`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_rank` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` char(128) collate utf8_unicode_ci NOT NULL COMMENT 'Rank name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`),
  CONSTRAINT `taxonomy_rank_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy ranks.';
SET character_set_client = @saved_cs_client;

/*!50003 SET @SAVE_SQL_MODE=@@SQL_MODE*/;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `drop_history_rank` AFTER DELETE ON `taxonomy_rank` FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@SAVE_SQL_MODE*/;

--
-- Temporary table structure for view `taxonomy_rank_norm`
--

DROP TABLE IF EXISTS `taxonomy_rank_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_norm`*/;
/*!50001 CREATE TABLE `taxonomy_rank_norm` (
  `rank_id` bigint(20) unsigned,
  `rank_name` char(128)
) ENGINE=MyISAM */;

--
-- Table structure for table `taxonomy_tree`
--

DROP TABLE IF EXISTS `taxonomy_tree`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `taxonomy_tree` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Primary key.',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Tree name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'Data history.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `history_id` (`history_id`),
  CONSTRAINT `taxonomy_tree_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `taxonomy_tree_norm`
--

DROP TABLE IF EXISTS `taxonomy_tree_norm`;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_norm`*/;
/*!50001 CREATE TABLE `taxonomy_tree_norm` (
  `tree_id` bigint(20) unsigned,
  `tree_name` varchar(255)
) ENGINE=MyISAM */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'User id.',
  `name` char(32) collate utf8_unicode_ci NOT NULL COMMENT 'User name.',
  `complete_name` varchar(512) collate utf8_unicode_ci default NULL COMMENT 'User full name.',
  `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'User creation time.',
  `password` char(32) collate utf8_unicode_ci NOT NULL COMMENT 'User password md5 hashed.',
  `email` varchar(129) collate utf8_unicode_ci default NULL COMMENT 'User email.',
  `user_type` enum('user','admin') collate utf8_unicode_ci NOT NULL default 'user' COMMENT 'User type.',
  `birthday` date default NULL COMMENT 'User''s birthday.',
  `image` blob COMMENT 'User''s image file.',
  `enabled` tinyint(1) NOT NULL default '1' COMMENT 'Enable/Disable user.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User''s table.';
SET character_set_client = @saved_cs_client;

/*!50003 SET @SAVE_SQL_MODE=@@SQL_MODE*/;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `user_md5_password_insert` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.password = MD5( NEW.password ) */;;

/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `user_md5_password_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
  IF NEW.password <> OLD.password THEN
    SET NEW.password = MD5( NEW.password );
  END IF;
END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@SAVE_SQL_MODE*/;

--
-- Final view structure for view `label_norm`
--

/*!50001 DROP TABLE `label_norm`*/;
/*!50001 DROP VIEW IF EXISTS `label_norm`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_norm` AS select `label`.`id` AS `label_id`,`label`.`type` AS `type`,`label`.`name` AS `name`,`label`.`autoadd` AS `autoadd`,`label`.`default` AS `default`,`label`.`must_exist` AS `must_exist`,`label`.`auto_on_creation` AS `auto_on_creation`,`label`.`auto_on_modification` AS `auto_on_modification`,`label`.`code` AS `code`,`label`.`deletable` AS `deletable`,`label`.`editable` AS `editable` from `label` */;

--
-- Final view structure for view `label_sequence_info`
--

/*!50001 DROP TABLE `label_sequence_info`*/;
/*!50001 DROP VIEW IF EXISTS `label_sequence_info`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `label_sequence_info` AS select `label_sequence`.`label_id` AS `label_id`,`label_sequence`.`id` AS `id`,`label_sequence`.`seq_id` AS `seq_id`,`label_sequence`.`subname` AS `subname`,`label_sequence`.`history_id` AS `history_id`,`label_sequence`.`int_data` AS `int_data`,`label_sequence`.`text_data` AS `text_data`,`label_sequence`.`obj_data` AS `obj_data`,`label_sequence`.`ref_data` AS `ref_data`,`label_sequence`.`position_a_data` AS `position_a_data`,`label_sequence`.`position_b_data` AS `position_b_data`,`label_sequence`.`taxonomy_data` AS `taxonomy_data`,`label_sequence`.`url_data` AS `url_data`,`label_norm`.`type` AS `type`,`label_norm`.`name` AS `name`,`label_norm`.`autoadd` AS `autoadd`,`label_norm`.`default` AS `default`,`label_norm`.`must_exist` AS `must_exist`,`label_norm`.`auto_on_creation` AS `auto_on_creation`,`label_norm`.`auto_on_modification` AS `auto_on_modification`,`label_norm`.`code` AS `code`,`label_norm`.`deletable` AS `deletable`,`label_norm`.`editable` AS `editable` from (`label_sequence` join `label_norm` on((`label_sequence`.`label_id` = `label_norm`.`label_id`))) */;

--
-- Final view structure for view `taxonomy_all_names`
--

/*!50001 DROP TABLE `taxonomy_all_names`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_all_names`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_all_names` AS select `taxonomy_only_names`.`id` AS `id`,`taxonomy_only_names`.`name` AS `name` from `taxonomy_only_names` union select `taxonomy_name_only_names`.`id` AS `id`,`taxonomy_name_only_names`.`name` AS `name` from `taxonomy_name_only_names` */;

--
-- Final view structure for view `taxonomy_info`
--

/*!50001 DROP TABLE `taxonomy_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_info`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_info` AS select `taxonomy`.`id` AS `id`,`taxonomy`.`name` AS `name`,`taxonomy`.`rank_id` AS `rank_id`,`taxonomy`.`tree_id` AS `tree_id`,`taxonomy`.`parent_id` AS `parent_id`,`taxonomy_parent_norm`.`parent_name` AS `parent_name`,`taxonomy_rank_norm`.`rank_name` AS `rank_name`,`taxonomy_tree_norm`.`tree_name` AS `tree_name` from (((`taxonomy` join `taxonomy_rank_norm` on((`taxonomy`.`rank_id` = `taxonomy_rank_norm`.`rank_id`))) left join `taxonomy_tree_norm` on((`taxonomy`.`tree_id` = `taxonomy_tree_norm`.`tree_id`))) left join `taxonomy_parent_norm` on((`taxonomy`.`parent_id` = `taxonomy_parent_norm`.`parent_id`))) */;

--
-- Final view structure for view `taxonomy_name_info`
--

/*!50001 DROP TABLE `taxonomy_name_info`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_info`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_info` AS select `taxonomy_name`.`id` AS `id`,`taxonomy_name`.`name` AS `name`,`taxonomy_name`.`tax_id` AS `tax_id`,`taxonomy_name`.`type_id` AS `type_id`,`taxonomy_name_type_norm`.`type_name` AS `type_name` from (`taxonomy_name` join `taxonomy_name_type_norm` on((`taxonomy_name`.`type_id` = `taxonomy_name_type_norm`.`type_id`))) */;

--
-- Final view structure for view `taxonomy_name_only_names`
--

/*!50001 DROP TABLE `taxonomy_name_only_names`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_only_names`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_only_names` AS select `taxonomy_name`.`tax_id` AS `id`,`taxonomy_name`.`name` AS `name` from `taxonomy_name` */;

--
-- Final view structure for view `taxonomy_name_type_norm`
--

/*!50001 DROP TABLE `taxonomy_name_type_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_name_type_norm`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_name_type_norm` AS select `taxonomy_name_type`.`id` AS `type_id`,`taxonomy_name_type`.`name` AS `type_name` from `taxonomy_name_type` */;

--
-- Final view structure for view `taxonomy_only_names`
--

/*!50001 DROP TABLE `taxonomy_only_names`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_only_names`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_only_names` AS select `taxonomy`.`id` AS `id`,`taxonomy`.`name` AS `name` from `taxonomy` */;

--
-- Final view structure for view `taxonomy_parent`
--

/*!50001 DROP TABLE `taxonomy_parent`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_parent`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_parent` AS select `taxonomy`.`id` AS `parent`,`taxonomy`.`name` AS `parent_name` from `taxonomy` */;

--
-- Final view structure for view `taxonomy_parent_norm`
--

/*!50001 DROP TABLE `taxonomy_parent_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_parent_norm`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_parent_norm` AS select `taxonomy`.`id` AS `parent_id`,`taxonomy`.`name` AS `parent_name` from `taxonomy` where (`taxonomy`.`parent_id` <> NULL) */;

--
-- Final view structure for view `taxonomy_rank_norm`
--

/*!50001 DROP TABLE `taxonomy_rank_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_rank_norm`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_rank_norm` AS (select `taxonomy_rank`.`id` AS `rank_id`,`taxonomy_rank`.`name` AS `rank_name` from `taxonomy_rank`) */;

--
-- Final view structure for view `taxonomy_tree_norm`
--

/*!50001 DROP TABLE `taxonomy_tree_norm`*/;
/*!50001 DROP VIEW IF EXISTS `taxonomy_tree_norm`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `taxonomy_tree_norm` AS select `taxonomy_tree`.`id` AS `tree_id`,`taxonomy_tree`.`name` AS `tree_name` from `taxonomy_tree` */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-02-18 18:17:33
