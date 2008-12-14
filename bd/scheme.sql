-- phpMyAdmin SQL Dump
-- version 3.0.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 14, 2008 at 02:24 PM
-- Server version: 5.0.68
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `FDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE IF NOT EXISTS `history` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `creation_user_id` bigint(20) unsigned default NULL COMMENT 'User that created this.',
  `creation` timestamp NULL default CURRENT_TIMESTAMP COMMENT 'Date/Time of creation.',
  `update_user_id` bigint(20) unsigned default NULL COMMENT 'User that modified this.',
  `update` timestamp NULL default NULL COMMENT 'Date/Time of last modification.',
  PRIMARY KEY  (`id`),
  KEY `creation_user_id` (`creation_user_id`),
  KEY `update_user_id` (`update_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='history' AUTO_INCREMENT=34 ;

--
-- Triggers `history`
--
DROP TRIGGER IF EXISTS `FDB`.`add_history_trigger`;
DELIMITER //
CREATE TRIGGER `FDB`.`add_history_trigger` BEFORE INSERT ON `FDB`.`history`
 FOR EACH ROW BEGIN
    SET NEW.`update` = NOW();
    SET NEW.`creation_user_id` = NEW.`update_user_id`;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `FDB`.`update_history_trigger`;
DELIMITER //
CREATE TRIGGER `FDB`.`update_history_trigger` BEFORE UPDATE ON `FDB`.`history`
 FOR EACH ROW BEGIN
    set @author = OLD.creation_user_id;
    IF (@author IS NULL) THEN
      SET NEW.creation_user_id = NEW.update_user_id;
    END IF;
    SET NEW.`update` = NOW();
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
CREATE TABLE IF NOT EXISTS `label` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `type` enum('integer','text','obj','position','ref','tax') collate utf8_unicode_ci NOT NULL COMMENT 'Label type.',
  `name` char(255) collate utf8_unicode_ci NOT NULL COMMENT 'Label type name.',
  `comment` varchar(1024) collate utf8_unicode_ci default NULL COMMENT 'Label type comment.',
  `autoadd` tinyint(1) NOT NULL default '0' COMMENT 'Auto add to new sequences?',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  `default` tinyint(1) NOT NULL default '0' COMMENT 'Is this a default label?',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label types.' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `label`
--

INSERT INTO `label` (`id`, `type`, `name`, `comment`, `autoadd`, `history_id`, `default`) VALUES
(1, 'integer', 'length', NULL, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `label_sequence`
--

DROP TABLE IF EXISTS `label_sequence`;
CREATE TABLE IF NOT EXISTS `label_sequence` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `seq_id` bigint(20) unsigned NOT NULL COMMENT 'Sequence id.',
  `label_id` bigint(20) unsigned NOT NULL COMMENT 'Label id.',
  `subname` varchar(512) collate utf8_unicode_ci default NULL COMMENT 'Sub name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  `int_data` int(11) default NULL COMMENT 'Integer data.',
  `text_data` varchar(1024) collate utf8_unicode_ci default NULL COMMENT 'Text data.',
  `obj_data` blob COMMENT 'Object data.',
  `ref_data` bigint(20) default NULL COMMENT 'Reference Data.',
  `position_a_data` int(11) default NULL COMMENT 'Position data A.',
  `position_b_data` int(11) default NULL COMMENT 'Position data B.',
  `taxonomy_data` bigint(20) default NULL COMMENT 'Taxonomy data.',
  PRIMARY KEY  (`id`),
  KEY `history_id` (`history_id`),
  KEY `label_id` (`label_id`),
  KEY `seq_id` (`seq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Label''s of sequences.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sequence`
--

DROP TABLE IF EXISTS `sequence`;
CREATE TABLE IF NOT EXISTS `sequence` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `content` text character set ascii NOT NULL COMMENT 'Sequence itself.',
  `accession` char(255) collate utf8_unicode_ci default NULL COMMENT 'Accession number.',
  `type` enum('dna','protein') collate utf8_unicode_ci NOT NULL default 'dna' COMMENT 'Sequence type.',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Sequence name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  PRIMARY KEY  (`id`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sequences table.' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy`
--

DROP TABLE IF EXISTS `taxonomy`;
CREATE TABLE IF NOT EXISTS `taxonomy` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `parent_id` int(11) default NULL COMMENT 'Parent taxonomy.',
  `rank_id` bigint(20) unsigned NOT NULL COMMENT 'Rank.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  PRIMARY KEY  (`id`),
  KEY `rank_index` (`rank_id`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy table.' AUTO_INCREMENT=20 ;

--
-- Triggers `taxonomy`
--
DROP TRIGGER IF EXISTS `FDB`.`drop_history_taxonomy`;
DELIMITER //
CREATE TRIGGER `FDB`.`drop_history_taxonomy` AFTER DELETE ON `FDB`.`taxonomy`
 FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_all_names`
--
DROP VIEW IF EXISTS `taxonomy_all_names`;
CREATE TABLE IF NOT EXISTS `taxonomy_all_names` (
`id` bigint(20) unsigned
,`name` varchar(512)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_and_rank`
--
DROP VIEW IF EXISTS `taxonomy_and_rank`;
CREATE TABLE IF NOT EXISTS `taxonomy_and_rank` (
`id` bigint(20) unsigned
,`name` varchar(512)
,`rank_id` bigint(20) unsigned
,`parent_id` int(11)
,`rank_name` char(128)
);
-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_name`
--

DROP TABLE IF EXISTS `taxonomy_name`;
CREATE TABLE IF NOT EXISTS `taxonomy_name` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  `tax_id` bigint(20) unsigned NOT NULL COMMENT 'Taxonomy ID.',
  `type_id` bigint(20) unsigned NOT NULL COMMENT 'Name type ID.',
  PRIMARY KEY  (`id`),
  KEY `tax_id` (`tax_id`,`type_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Taxonomy names.' AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_name_and_type`
--
DROP VIEW IF EXISTS `taxonomy_name_and_type`;
CREATE TABLE IF NOT EXISTS `taxonomy_name_and_type` (
`id` bigint(20) unsigned
,`name` varchar(512)
,`tax_id` bigint(20) unsigned
,`type_id` bigint(20) unsigned
,`type_name` varchar(512)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_name_only_names`
--
DROP VIEW IF EXISTS `taxonomy_name_only_names`;
CREATE TABLE IF NOT EXISTS `taxonomy_name_only_names` (
`id` bigint(20) unsigned
,`name` varchar(512)
);
-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_name_type`
--

DROP TABLE IF EXISTS `taxonomy_name_type`;
CREATE TABLE IF NOT EXISTS `taxonomy_name_type` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` varchar(512) collate utf8_unicode_ci NOT NULL COMMENT 'Name.',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Types of names for taxonomies.' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `taxonomy_name_type`
--

INSERT INTO `taxonomy_name_type` (`id`, `name`) VALUES
(1, 'synonym'),
(2, 'authority');

-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_name_type_b`
--
DROP VIEW IF EXISTS `taxonomy_name_type_b`;
CREATE TABLE IF NOT EXISTS `taxonomy_name_type_b` (
`type_id` bigint(20) unsigned
,`type_name` varchar(512)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_only_names`
--
DROP VIEW IF EXISTS `taxonomy_only_names`;
CREATE TABLE IF NOT EXISTS `taxonomy_only_names` (
`id` bigint(20) unsigned
,`name` varchar(512)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_parent`
--
DROP VIEW IF EXISTS `taxonomy_parent`;
CREATE TABLE IF NOT EXISTS `taxonomy_parent` (
`parent` bigint(20) unsigned
,`parent_name` varchar(512)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_parent_rank`
--
DROP VIEW IF EXISTS `taxonomy_parent_rank`;
CREATE TABLE IF NOT EXISTS `taxonomy_parent_rank` (
`id` bigint(20) unsigned
,`name` varchar(512)
,`rank_id` bigint(20) unsigned
,`parent_id` int(11)
,`rank_name` char(128)
,`parent_name` varchar(512)
);
-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_rank`
--

DROP TABLE IF EXISTS `taxonomy_rank`;
CREATE TABLE IF NOT EXISTS `taxonomy_rank` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Key.',
  `name` char(128) collate utf8_unicode_ci NOT NULL COMMENT 'Rank name.',
  `history_id` bigint(20) unsigned default NULL COMMENT 'History.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Taxonomy ranks.' AUTO_INCREMENT=109 ;

--
-- Triggers `taxonomy_rank`
--
DROP TRIGGER IF EXISTS `FDB`.`drop_history_rank`;
DELIMITER //
CREATE TRIGGER `FDB`.`drop_history_rank` AFTER DELETE ON `FDB`.`taxonomy_rank`
 FOR EACH ROW BEGIN
delete from history where id = OLD.history_id;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `taxonomy_rank_b`
--
DROP VIEW IF EXISTS `taxonomy_rank_b`;
CREATE TABLE IF NOT EXISTS `taxonomy_rank_b` (
`rank_id` bigint(20) unsigned
,`rank_name` char(128)
);
-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'User id.',
  `name` char(32) collate utf8_unicode_ci NOT NULL COMMENT 'User name.',
  `complete_name` varchar(512) collate utf8_unicode_ci default NULL COMMENT 'User full name.',
  `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'User creation time.',
  `password` char(32) collate utf8_unicode_ci NOT NULL COMMENT 'User password md5 hashed.',
  `email` varchar(129) collate utf8_unicode_ci default NULL COMMENT 'User email.',
  `user_type` enum('user','admin') collate utf8_unicode_ci NOT NULL default 'user' COMMENT 'User type.',
  `birthday` date default NULL COMMENT 'User''s birthday.',
  `image` blob COMMENT 'User''s image file.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User''s table.' AUTO_INCREMENT=6 ;

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `FDB`.`user_md5_password_insert`;
DELIMITER //
CREATE TRIGGER `FDB`.`user_md5_password_insert` BEFORE INSERT ON `FDB`.`user`
 FOR EACH ROW SET NEW.password = MD5( NEW.password )
//
DELIMITER ;
DROP TRIGGER IF EXISTS `FDB`.`user_md5_password_update`;
DELIMITER //
CREATE TRIGGER `FDB`.`user_md5_password_update` BEFORE UPDATE ON `FDB`.`user`
 FOR EACH ROW BEGIN
  IF NEW.password <> OLD.password THEN
    SET NEW.password = MD5( NEW.password );
  END IF;
END
//
DELIMITER ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`name`, `complete_name`, `creation_date`, `password`, `email`, `user_type`, `birthday`, `image`) VALUES
('flavio', 'flavio cruz', '2008-12-06 15:58:03', 'ibmc123', 'flaviocruz@gmail.com', 'admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_all_names`
--
DROP TABLE IF EXISTS `taxonomy_all_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_all_names` AS select `taxonomy_only_names`.`id` AS `id`,`taxonomy_only_names`.`name` AS `name` from `FDB`.`taxonomy_only_names` union select `taxonomy_name_only_names`.`id` AS `id`,`taxonomy_name_only_names`.`name` AS `name` from `FDB`.`taxonomy_name_only_names`;

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_and_rank`
--
DROP TABLE IF EXISTS `taxonomy_and_rank`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_and_rank` AS select `FDB`.`taxonomy`.`id` AS `id`,`FDB`.`taxonomy`.`name` AS `name`,`FDB`.`taxonomy`.`rank_id` AS `rank_id`,`FDB`.`taxonomy`.`parent_id` AS `parent_id`,`taxonomy_rank_b`.`rank_name` AS `rank_name` from (`FDB`.`taxonomy` join `FDB`.`taxonomy_rank_b` on((`FDB`.`taxonomy`.`rank_id` = `taxonomy_rank_b`.`rank_id`)));

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_name_and_type`
--
DROP TABLE IF EXISTS `taxonomy_name_and_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_name_and_type` AS select `FDB`.`taxonomy_name`.`id` AS `id`,`FDB`.`taxonomy_name`.`name` AS `name`,`FDB`.`taxonomy_name`.`tax_id` AS `tax_id`,`FDB`.`taxonomy_name`.`type_id` AS `type_id`,`taxonomy_name_type_b`.`type_name` AS `type_name` from (`FDB`.`taxonomy_name` join `FDB`.`taxonomy_name_type_b` on((`FDB`.`taxonomy_name`.`type_id` = `taxonomy_name_type_b`.`type_id`)));

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_name_only_names`
--
DROP TABLE IF EXISTS `taxonomy_name_only_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_name_only_names` AS select `FDB`.`taxonomy_name`.`tax_id` AS `id`,`FDB`.`taxonomy_name`.`name` AS `name` from `FDB`.`taxonomy_name`;

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_name_type_b`
--
DROP TABLE IF EXISTS `taxonomy_name_type_b`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_name_type_b` AS select `FDB`.`taxonomy_name_type`.`id` AS `type_id`,`FDB`.`taxonomy_name_type`.`name` AS `type_name` from `FDB`.`taxonomy_name_type`;

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_only_names`
--
DROP TABLE IF EXISTS `taxonomy_only_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_only_names` AS select `FDB`.`taxonomy`.`id` AS `id`,`FDB`.`taxonomy`.`name` AS `name` from `FDB`.`taxonomy`;

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_parent`
--
DROP TABLE IF EXISTS `taxonomy_parent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_parent` AS select `FDB`.`taxonomy`.`id` AS `parent`,`FDB`.`taxonomy`.`name` AS `parent_name` from `FDB`.`taxonomy`;

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_parent_rank`
--
DROP TABLE IF EXISTS `taxonomy_parent_rank`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_parent_rank` AS select `tr`.`id` AS `id`,`tr`.`name` AS `name`,`tr`.`rank_id` AS `rank_id`,`tr`.`parent_id` AS `parent_id`,`tr`.`rank_name` AS `rank_name`,`tp`.`parent_name` AS `parent_name` from (`FDB`.`taxonomy_and_rank` `tr` left join `FDB`.`taxonomy_parent` `tp` on((`tr`.`parent_id` = `tp`.`parent`)));

-- --------------------------------------------------------

--
-- Structure for view `taxonomy_rank_b`
--
DROP TABLE IF EXISTS `taxonomy_rank_b`;

CREATE ALGORITHM=UNDEFINED DEFINER=`flavio`@`localhost` SQL SECURITY DEFINER VIEW `FDB`.`taxonomy_rank_b` AS select `FDB`.`taxonomy_rank`.`id` AS `rank_id`,`FDB`.`taxonomy_rank`.`name` AS `rank_name` from `FDB`.`taxonomy_rank`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`creation_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`update_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `label`
--
ALTER TABLE `label`
  ADD CONSTRAINT `label_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `label_sequence`
--
ALTER TABLE `label_sequence`
  ADD CONSTRAINT `label_sequence_ibfk_1` FOREIGN KEY (`seq_id`) REFERENCES `sequence` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `label_sequence_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `label_sequence_ibfk_3` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sequence`
--
ALTER TABLE `sequence`
  ADD CONSTRAINT `sequence_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `taxonomy`
--
ALTER TABLE `taxonomy`
  ADD CONSTRAINT `taxonomy_ibfk_1` FOREIGN KEY (`rank_id`) REFERENCES `taxonomy_rank` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `taxonomy_ibfk_2` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxonomy_name`
--
ALTER TABLE `taxonomy_name`
  ADD CONSTRAINT `taxonomy_name_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `taxonomy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `taxonomy_name_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `taxonomy_name_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxonomy_rank`
--
ALTER TABLE `taxonomy_rank`
  ADD CONSTRAINT `taxonomy_rank_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE SET NULL;

