-- MySQL dump 10.13  Distrib 5.1.44, for apple-darwin8.11.1 (i386)
--
-- Host: localhost    Database: digital_tiger
-- ------------------------------------------------------
-- Server version	5.1.44

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
-- Table structure for table `affiliate_levels`
--

DROP TABLE IF EXISTS `affiliate_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliate_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `percentage` decimal(2,2) NOT NULL,
  `recurring` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliate_levels`
--

LOCK TABLES `affiliate_levels` WRITE;
/*!40000 ALTER TABLE `affiliate_levels` DISABLE KEYS */;
INSERT INTO `affiliate_levels` VALUES (1,'Affiliate','0.15',0),(2,'Reseller','0.30',1),(3,'Expert','0.30',1);
/*!40000 ALTER TABLE `affiliate_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affiliate_payments`
--

DROP TABLE IF EXISTS `affiliate_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliate_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_code` varchar(5) NOT NULL,
  `date` int(11) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliate_payments`
--

LOCK TABLES `affiliate_payments` WRITE;
/*!40000 ALTER TABLE `affiliate_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliate_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affiliate_tracking`
--

DROP TABLE IF EXISTS `affiliate_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliate_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `organization_id` int(11) NOT NULL COMMENT 'Organization that signed up under the affiliate',
  `affiliate_code` varchar(5) NOT NULL COMMENT 'The affiliate that referred the user with user_id',
  `original_date` int(11) NOT NULL,
  `start_date` int(11) NOT NULL,
  `last_payment_date` int(11) NOT NULL,
  `initial_payment_made` int(1) DEFAULT '0',
  `tier` int(1) NOT NULL DEFAULT '0',
  `tier_child` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `affiliate_code` (`affiliate_code`),
  KEY `start_date` (`start_date`),
  KEY `initial_payment_made` (`initial_payment_made`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliate_tracking`
--

LOCK TABLES `affiliate_tracking` WRITE;
/*!40000 ALTER TABLE `affiliate_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliate_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authorized_devices`
--

DROP TABLE IF EXISTS `authorized_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authorized_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_hash` varchar(32) DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `date_last_access` int(11) DEFAULT NULL,
  `client_version` varchar(4) DEFAULT NULL,
  `os_string` varchar(60) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authorized_devices`
--

LOCK TABLES `authorized_devices` WRITE;
/*!40000 ALTER TABLE `authorized_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `authorized_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_auth_sessions`
--

DROP TABLE IF EXISTS `ci_auth_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_auth_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `device_hash` varchar(32) DEFAULT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_auth_sessions`
--

LOCK TABLES `ci_auth_sessions` WRITE;
/*!40000 ALTER TABLE `ci_auth_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_auth_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_sessions`
--

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
INSERT INTO `ci_sessions` VALUES ('8c9b570fe18ccd7b229ef8f59a61f252','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1304614490,'a:2:{s:13:\"authenticated\";s:4:\"true\";s:9:\"user_data\";a:28:{s:2:\"id\";s:1:\"1\";s:10:\"session_id\";s:13:\"4dc2c5117dd72\";s:10:\"date_added\";s:10:\"1272670664\";s:14:\"date_confirmed\";s:10:\"1284953579\";s:15:\"activation_code\";s:32:\"8ee95d7bfa10bc10a9ce47c6c4015da2\";s:10:\"first_name\";s:6:\"Morris\";s:9:\"last_name\";s:6:\"Mwanga\";s:13:\"email_address\";s:17:\"morris@nerd.co.ke\";s:17:\"new_email_address\";s:0:\"\";s:8:\"password\";s:0:\"\";s:13:\"sha1_password\";s:40:\"ff771755774e0b4a40c05102c24eb0d8f656e609\";s:15:\"organization_id\";s:3:\"105\";s:6:\"active\";s:1:\"1\";s:14:\"affiliate_code\";s:0:\"\";s:18:\"affiliate_ptd_code\";s:0:\"\";s:15:\"affiliate_level\";s:0:\"\";s:21:\"affiliate_parent_code\";s:0:\"\";s:10:\"last_login\";s:10:\"1303481421\";s:6:\"logins\";s:1:\"7\";s:20:\"affiliate_first_name\";s:0:\"\";s:19:\"affiliate_last_name\";s:0:\"\";s:16:\"affiliate_street\";s:0:\"\";s:18:\"affiliate_street_2\";s:0:\"\";s:14:\"affiliate_city\";s:0:\"\";s:15:\"affiliate_state\";s:0:\"\";s:13:\"affiliate_zip\";s:0:\"\";s:13:\"affiliate_tax\";s:0:\"\";s:17:\"organization_name\";s:0:\"\";}}');
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `digital_file_keys`
--

DROP TABLE IF EXISTS `digital_file_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `digital_file_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_type` varchar(60) DEFAULT NULL,
  `file_extension` varchar(32) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `date_created` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `s3_key` varchar(32) DEFAULT NULL,
  `thumb_small` tinyint(4) DEFAULT NULL,
  `thumb_large` tinyint(4) DEFAULT NULL,
  `indexing_status` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_file_keys`
--

LOCK TABLES `digital_file_keys` WRITE;
/*!40000 ALTER TABLE `digital_file_keys` DISABLE KEYS */;
INSERT INTO `digital_file_keys` VALUES (1,'application/vnd.openxmlformats-officedocument.wordprocessing','.docx',131,1303232079,0,'00028010000000130323207933823318',0,0,1),(2,'application/msword','.doc',1958,1303232087,0,'00001050000000130323208716131757',0,0,1),(3,'application/pdf','.pdf',10,1303232406,0,'00028010000000130323240685951457',0,0,1),(4,'application/vnd.oasis.opendocument.text','.odt',16,1303237294,0,'00001050000000130323729588278471',0,0,1),(5,'application/vnd.openxmlformats-officedocument.wordprocessing','.docx',129,1303237310,0,'00028010000000130323731016034995',0,0,1),(6,'application/pdf','.pdf',196,1303237645,0,'00001050000000130323764549825583',0,0,1),(7,'application/vnd.ms-excel','.xls',31,1303240878,0,'00028010000000130324087847737691',0,0,1),(8,'image/png','.png',400,1303241370,0,'00028010000000130324137023732837',0,0,1),(9,'application/pdf','.pdf',354,1303241669,0,'00028010000000130324166924747621',0,0,1),(10,'application/pdf','.pdf',354,1303243493,0,'00028010000000130324349364881463',0,0,1),(23,'application/vnd.openxmlformats-officedocument.wordprocessing','.docx',151,1303480006,0,'00028010000000130348000689955196',0,0,1),(22,'application/vnd.openxmlformats-officedocument.wordprocessing','.docx',77,1303401296,0,'00028010000000130340129672829755',0,0,1),(21,'application/vnd.openxmlformats-officedocument.wordprocessing','.docx',23,1303389574,0,'00028010000000130338957466418877',0,0,1),(20,'application/msword','.doc',1958,1303322744,0,'00001050000000130332274434515005',0,0,1),(19,'application/msword','.doc',1958,1303322195,0,'00001050000000130332219599541939',0,0,1),(24,'application/msword','.doc',1494,1303480133,0,'00028010000000130348013341258600',0,0,1),(25,'application/pdf','.pdf',127,1304610746,0,'00001050000000130461074666946343',0,0,0);
/*!40000 ALTER TABLE `digital_file_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `digital_file_status`
--

DROP TABLE IF EXISTS `digital_file_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `digital_file_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_status_id` int(11) NOT NULL,
  `file_status_description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_file_status`
--

LOCK TABLES `digital_file_status` WRITE;
/*!40000 ALTER TABLE `digital_file_status` DISABLE KEYS */;
INSERT INTO `digital_file_status` VALUES (1,1,'Sucessfully Indexed'),(2,2,'Indexing in Progress '),(3,0,'Pending Indexing'),(4,3,'Pending Upload');
/*!40000 ALTER TABLE `digital_file_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `digital_files`
--

DROP TABLE IF EXISTS `digital_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `digital_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_hash` varchar(32) NOT NULL,
  `encrypted_file_hash` varchar(32) DEFAULT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `original_file_path` varchar(255) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `revision_id` int(11) DEFAULT NULL,
  `upload_time` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `upload_source` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `upload_source` (`upload_source`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_files`
--

LOCK TABLES `digital_files` WRITE;
/*!40000 ALTER TABLE `digital_files` DISABLE KEYS */;
INSERT INTO `digital_files` VALUES (1,'75e773ef5ee240e65072fd311eabd439',NULL,'What_does_The_Monticello_Corporation_Offer_You.docx','/var/www/html/uploads/What_does_The_Monticello_Corporation_Offer_You.docx',2801,1,0,1303232080,2,1),(2,'d8a581f6904038d7d7cc337a71689f1b',NULL,'EvacuationGuide.doc','/var/www/html/uploads/EvacuationGuide.doc',105,2,0,1303232088,1,1),(3,'e59cbdff931ee7537cae6bcab2e5a3ac',NULL,'what_do_i_need.pdf','/var/www/html/uploads/what_do_i_need.pdf',2801,3,0,1303232407,2,1),(4,'2b338d0a5bec0347085c160357efa75e',NULL,'lorem_ipsum.odt','/var/www/html/uploads/lorem_ipsum1.odt',105,4,0,1303237295,1,1),(5,'580a806131023da6f31f9c52fd0b4e5a',NULL,'To-do:Projects-Janets.docx','/var/www/html/uploads/To-do:Projects-Janets.docx',2801,5,0,1303237311,2,1),(6,'c4ad99dae1880e48ad83807f6b211ccd',NULL,'word.pdf','/var/www/html/uploads/word.pdf',105,6,0,1303237646,1,1),(7,'e81f770779dd45818a043ba4dbfc2f08',NULL,'payments_20110412-20110414.xls','/var/www/html/uploads/payments_20110412-20110414.xls',2801,7,0,1303240879,2,1),(8,'ab4110ad61ec5a733b7bf97012f4eeae',NULL,'Screen_shot_2011-03-08_at_5.47_.52_PM_.png','/var/www/html/uploads/Screen_shot_2011-03-08_at_5.47_.52_PM_.png',2801,8,0,1303241371,2,1),(9,'b313f0301b656b5cafcb6fe0afbf83f3',NULL,'TAGS.pdf','/var/www/html/uploads/TAGS1.pdf',2801,9,0,1303241669,2,1),(10,'b313f0301b656b5cafcb6fe0afbf83f3',NULL,'TAGS.pdf','/var/www/html/uploads/TAGS2.pdf',2801,10,0,1303243493,2,1),(24,'28fa0e242b6a71bae143dc30a4bd58f8',NULL,'Automatic_Archive_Changes.doc','/var/www/html/uploads/Automatic_Archive_Changes.doc',2801,24,0,1303480133,2,1),(23,'72f5538b5a022ef31946c45558e5af80',NULL,'2-16_QAs.docx','/var/www/html/uploads/2-16_QAs.docx',2801,23,0,1303480006,2,1),(22,'9ec044d426eab0baf4ba398788d69f16',NULL,'email_draft_for_orders.docx','/var/www/html/uploads/email_draft_for_orders.docx',2801,22,0,1303401297,2,1),(20,'d8a581f6904038d7d7cc337a71689f1b',NULL,'EvacuationGuide.doc','/var/www/html/uploads/EvacuationGuide2.doc',105,20,0,1303322745,1,1),(21,'0cec1a201f1c832cbd573421e673525d',NULL,'Intuit_fax.docx','/var/www/html/uploads/Intuit_fax.docx',2801,21,0,1303389574,2,1),(19,'d8a581f6904038d7d7cc337a71689f1b',NULL,'EvacuationGuide.doc','/var/www/html/uploads/EvacuationGuide1.doc',105,19,0,1303322196,1,1),(25,'763871a5cdaba56d0a38682ed984d552',NULL,'10.1_.1_.3_.8335_.pdf','/Applications/XAMPP/xamppfiles/htdocs/digitaltiger/uploads/10.1_.1_.3_.8335_.pdf',105,25,0,1304610747,1,1);
/*!40000 ALTER TABLE `digital_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_logins`
--

DROP TABLE IF EXISTS `failed_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_logins` (
  `email_address` varchar(60) DEFAULT NULL,
  `ip_address` varchar(20) NOT NULL,
  `date` int(11) NOT NULL,
  KEY `ip_address` (`ip_address`),
  KEY `user_id` (`email_address`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_logins`
--

LOCK TABLES `failed_logins` WRITE;
/*!40000 ALTER TABLE `failed_logins` DISABLE KEYS */;
INSERT INTO `failed_logins` VALUES ('morris@nerd.co.ke','127.0.0.1',1302704135),('morris@nerd.co.ke','127.0.0.1',1302704135),('nkaronji@thepapertiger.com','104.6.6.17',1303305308),('nkaronji@thepapertiger.com','104.6.6.17',1303305308),('nkaronji@thepapertiger.com','104.6.6.17',1303305801),('nkaronji@thepapertiger.com','104.6.6.17',1303305801),('fholgado@gmail.com','104.6.6.17',1303389464),('fholgado@gmail.com','104.6.6.17',1303389464);
/*!40000 ALTER TABLE `failed_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_labels`
--

DROP TABLE IF EXISTS `file_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `label_id` (`label_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_labels`
--

LOCK TABLES `file_labels` WRITE;
/*!40000 ALTER TABLE `file_labels` DISABLE KEYS */;
INSERT INTO `file_labels` VALUES (0,0,2),(1,0,20),(2,1,20),(3,1,21),(4,1,19);
/*!40000 ALTER TABLE `file_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_storage`
--

DROP TABLE IF EXISTS `file_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `description` text,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_storage`
--

LOCK TABLES `file_storage` WRITE;
/*!40000 ALTER TABLE `file_storage` DISABLE KEYS */;
INSERT INTO `file_storage` VALUES (1,'Dropbox','Dropbox is a free service that lets you bring your photos, docs, and videos anywhere and share them easily. Never email yourself a file again!',1);
/*!40000 ALTER TABLE `file_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_upload_requests`
--

DROP TABLE IF EXISTS `file_upload_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_upload_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL,
  `file_hash` varchar(32) DEFAULT NULL,
  `file_path` varchar(60) DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `time_stamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_upload_requests`
--

LOCK TABLES `file_upload_requests` WRITE;
/*!40000 ALTER TABLE `file_upload_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_upload_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_api_sessions`
--

DROP TABLE IF EXISTS `incoming_api_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_api_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `client_hash` varchar(32) DEFAULT NULL,
  `client_version` varchar(4) DEFAULT NULL,
  `computer_name` varchar(60) DEFAULT NULL,
  `os_string` varchar(60) DEFAULT NULL,
  `action` varchar(15) DEFAULT NULL,
  `remote_addr` varchar(19) DEFAULT NULL,
  `time_stamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_api_sessions`
--

LOCK TABLES `incoming_api_sessions` WRITE;
/*!40000 ALTER TABLE `incoming_api_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `incoming_api_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_name` varchar(255) DEFAULT NULL,
  `label_color` varchar(6) DEFAULT NULL,
  `font_color` varchar(6) DEFAULT NULL,
  `label_notes` varchar(255) DEFAULT NULL,
  `label_owner_id` int(11) DEFAULT NULL,
  `label_org_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `labels`
--

LOCK TABLES `labels` WRITE;
/*!40000 ALTER TABLE `labels` DISABLE KEYS */;
INSERT INTO `labels` VALUES (0,'Morris Top Secret Files','FF0000','000000','Top secret unreleased files',1,1),(1,'Morris Declassified Files','FF0000','000000','Top secret unreleased files',1,1);
/*!40000 ALTER TABLE `labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `subscription_id` varchar(11) NOT NULL COMMENT 'this is assigned by the billing api',
  `admin_user` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `is_canceled` tinyint(4) NOT NULL,
  `is_suspended` tinyint(4) NOT NULL,
  `suspended_date` int(11) NOT NULL,
  `extra_user_slots` int(3) NOT NULL,
  `force_ssl` tinyint(1) NOT NULL,
  `newsletter` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `admin_user` (`admin_user`),
  KEY `package_id` (`package_id`),
  CONSTRAINT `organizations_ibfk_1` FOREIGN KEY (`admin_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `organizations_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2802 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,1272670664,'KSU','0',1,8,0,0,0,0,1,1),(2801,1303231596,'Monticello','0',2,1,0,0,0,0,0,1);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL COMMENT 'definitive what package is better that which. need a simple number to use for upgrade/downgrade',
  `name` varchar(40) NOT NULL,
  `plan_description` varchar(60) NOT NULL COMMENT 'Description field for plans',
  `mailing_list` varchar(30) DEFAULT NULL,
  `price` decimal(5,2) NOT NULL,
  `billing_cycle` varchar(5) DEFAULT NULL,
  `groups` tinyint(4) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `max_databases` tinyint(4) NOT NULL,
  `max_users` int(11) NOT NULL,
  `allow_extra_users` tinyint(1) NOT NULL COMMENT 'unused row i think, all packages can add users?',
  `max_locations` int(11) NOT NULL,
  `max_categories` int(11) NOT NULL,
  `max_items` int(11) NOT NULL,
  `allow_reports` tinyint(4) NOT NULL,
  `allow_backups` tinyint(1) NOT NULL,
  `allow_ssl` tinyint(4) NOT NULL,
  `allow_db_import` tinyint(4) NOT NULL,
  `labels` tinyint(4) NOT NULL,
  `allow_transfers` tinyint(4) NOT NULL,
  `advanced_search` tinyint(4) NOT NULL,
  `find_first` tinyint(4) NOT NULL,
  `active_msg` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='0 for any of the max means unlimited.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (1,1,'Free','Free','Paper Tiger Online Free','0.00','month',0,1,1,1,0,1,-1,100,0,1,1,0,0,0,0,0,6),(2,4,'Pro','Pro','Paper Tiger Online Pro','9.00','month',1,1,-1,1,0,-1,-1,-1,1,1,1,1,1,1,1,1,6),(3,5,'ProYearly','Pro Yearly','Paper Tiger Online Pro','90.00','year',1,1,-1,1,0,-1,-1,-1,1,1,1,1,1,1,1,1,5),(4,0,'Beta','Beta Tester','Paper Tiger Online Free','0.00','month',98,1,-1,1,0,-1,-1,-1,1,1,1,1,1,1,1,1,0),(5,7,'Business','Business','Paper Tiger Online Business','19.00','month',2,1,-1,10,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(6,8,'BusinessYearly','BusinessYearly','Paper Tiger Online Business','190.00','year',2,1,-1,10,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(7,13,'Unlimited','Unlimited','Paper Tiger Online Unlimited','59.00','month',3,1,-1,-1,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(8,14,'UnlimitedYearly','UnlimitedYearly','Paper Tiger Online Unlimited','590.00','year',3,1,-1,-1,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(9,2,'Basic','Basic','Paper Tiger Online Basic','5.00','month',4,1,1,1,0,2,-1,-1,1,1,1,0,1,1,0,1,6),(10,3,'BasicYearly','BasicYearly','Paper Tiger Online Basic','50.00','year',4,1,1,1,0,2,-1,-1,1,1,1,0,1,1,0,1,6),(11,10,'Elite','Elite','Paper Tiger Online Elite','39.00','month',5,1,-1,30,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(12,11,'EliteYearly','EliteYearly','Paper Tiger Online Elite','390.00','year',5,1,-1,30,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(13,6,'ProYearlySpecial','Pro Special','Paper Tiger Online Pro','75.60','year',6,1,-1,1,0,-1,-1,-1,1,1,1,1,1,1,1,1,6),(14,99,'FreeYearly','Free Pro Yearly','Paper Tiger Online Free','0.00','year',97,1,-1,1,0,-1,-1,-1,1,1,1,1,1,1,1,1,6),(15,9,'BusinessYearlySpecial','Business Special','Paper Tiger Online Business','159.60','year',6,1,-1,10,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(16,12,'EliteYearlySpecial','Elite Special','Paper Tiger Online Elite','327.60','year',6,1,-1,30,1,-1,-1,-1,1,1,1,1,1,1,1,1,6),(17,15,'UnlimitedYearlySpecial','Unlimited Special','Paper Tiger Online Unlimited','495.60','year',6,1,-1,-1,1,-1,-1,-1,1,1,1,1,1,1,1,1,6);
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `storage_auth_codes`
--

DROP TABLE IF EXISTS `storage_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `storage_auth_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `date_authenticated` int(11) DEFAULT NULL,
  `authentication_expiry` int(11) DEFAULT NULL,
  `suspended` tinyint(4) DEFAULT NULL,
  `storage_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `storage_id` (`storage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `storage_auth_codes`
--

LOCK TABLES `storage_auth_codes` WRITE;
/*!40000 ALTER TABLE `storage_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `storage_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_source`
--

DROP TABLE IF EXISTS `upload_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_source_id` int(11) NOT NULL,
  `upload_source_description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload_source`
--

LOCK TABLES `upload_source` WRITE;
/*!40000 ALTER TABLE `upload_source` DISABLE KEYS */;
INSERT INTO `upload_source` VALUES (1,1,'Web Uploader'),(2,2,'Desktop Uploader'),(3,3,'Email Attachment');
/*!40000 ALTER TABLE `upload_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(50) DEFAULT NULL,
  `date_added` int(11) NOT NULL,
  `date_confirmed` int(11) NOT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email_address` varchar(60) NOT NULL,
  `new_email_address` varchar(60) DEFAULT NULL COMMENT 'Keep Users new email address pendng here until the confirmation is successful',
  `password` text NOT NULL,
  `sha1_password` varchar(60) DEFAULT NULL COMMENT 'salt=7D9290F21C5BFBC99339C465D79567BB',
  `organization_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `affiliate_code` varchar(5) DEFAULT NULL,
  `affiliate_ptd_code` varchar(6) DEFAULT NULL,
  `affiliate_level` int(2) DEFAULT NULL COMMENT '1 - affiliate 15%, 2 - reseller 30%',
  `affiliate_parent_code` varchar(5) DEFAULT NULL,
  `last_login` int(11) DEFAULT '0',
  `logins` int(11) DEFAULT '0',
  `affiliate_first_name` varchar(255) NOT NULL,
  `affiliate_last_name` varchar(255) NOT NULL,
  `affiliate_street` varchar(255) NOT NULL,
  `affiliate_street_2` varchar(255) NOT NULL,
  `affiliate_city` varchar(255) NOT NULL,
  `affiliate_state` varchar(255) NOT NULL,
  `affiliate_zip` varchar(5) NOT NULL,
  `affiliate_tax` varchar(100) NOT NULL COMMENT 'Encrypted with CI',
  `storage_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_code` (`affiliate_code`),
  KEY `organization_id` (`organization_id`),
  KEY `storage_id` (`storage_id`),
  CONSTRAINT `users_fk_1` FOREIGN KEY (`storage_id`) REFERENCES `storage_auth_codes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'4dc2c5117dd72',1272670664,1284953579,'8ee95d7bfa10bc10a9ce47c6c4015da2','Morris','Mwanga','morris@nerd.co.ke',NULL,'','ff771755774e0b4a40c05102c24eb0d8f656e609',105,1,NULL,NULL,NULL,NULL,1304610065,8,'','','','','','','','',NULL),(2,'4db1867185aa7',1303231596,0,NULL,'Federico','Holgado','fholgado@gmail.com',NULL,'','9b82049a8bde02ad974b5010c7fa29993186002d',2801,1,NULL,NULL,NULL,NULL,1303479921,6,'','','','','','','','',NULL),(3,'4daeee6d1e941',1273247256,1282143893,NULL,'Nicholas','Karonji','nkaronji@thepapertiger.com',NULL,'','687cfb6f9064c449579b1220ca8fbe445743703a',204,1,NULL,NULL,NULL,NULL,1303309933,1,'','','','','','','','',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-05-05 12:59:32
