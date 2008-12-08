-- MySQL dump 10.11
--
-- Host: localhost    Database: bio
-- ------------------------------------------------------
-- Server version	5.0.68

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
  `birthdate` date default NULL COMMENT 'User''s birthday.',
  `image` blob COMMENT 'User''s image file.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User''s table.';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'flavio','flavio cruz','2008-11-08 21:03:15','3b1fbc05926bb948aa2ef4cebe09315d','flaviocruz@gmail.com','admin',NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

/*!50003 SET @SAVE_SQL_MODE=@@SQL_MODE*/;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `user_md5_password_insert` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.password = MD5( NEW.password ) */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`flavio`@`localhost` */ /*!50003 TRIGGER `user_md5_password_update` BEFORE UPDATE ON `user` FOR EACH ROW SET NEW.password = MD5( NEW.password ) */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@SAVE_SQL_MODE*/;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-11-16 21:49:26
