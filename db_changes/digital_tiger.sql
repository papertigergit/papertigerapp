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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_file_keys`
--

LOCK TABLES `digital_file_keys` WRITE;
/*!40000 ALTER TABLE `digital_file_keys` DISABLE KEYS */;
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
  `indexing_status` int(2) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `revision_id` int(11) DEFAULT NULL,
  `upload_time` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `upload_source` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `indexing_status` (`indexing_status`),
  KEY `upload_source` (`upload_source`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_files`
--

LOCK TABLES `digital_files` WRITE;
/*!40000 ALTER TABLE `digital_files` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `failed_logins` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2796 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0 for any of the max means unlimited.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_code` (`affiliate_code`),
  KEY `organization_id` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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

-- Dump completed on 2011-04-08 11:07:13
