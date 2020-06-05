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
INSERT INTO `ci_sessions` VALUES ('1ca5878a45986fddc7d46a1f78038a69','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1302704130,''),('3c9151bdbf00af8069a8f283ecd1af0e','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1302704162,''),('231dc7c388955f422069fe5c6a9408e8','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1302704178,''),('a143e621ca9b42236ffa0eea49fe6c63','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1302704208,''),('ffd1c6dbefcf90964f29ec06c38ff759','127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1302706931,'a:2:{s:13:\"authenticated\";s:4:\"true\";s:9:\"user_data\";a:28:{s:2:\"id\";s:1:\"1\";s:10:\"session_id\";s:13:\"4da5b89aad03d\";s:10:\"date_added\";s:10:\"1272670664\";s:14:\"date_confirmed\";s:10:\"1284953579\";s:15:\"activation_code\";s:32:\"8ee95d7bfa10bc10a9ce47c6c4015da2\";s:10:\"first_name\";s:6:\"Morris\";s:9:\"last_name\";s:6:\"Mwanga\";s:13:\"email_address\";s:17:\"morris@nerd.co.ke\";s:17:\"new_email_address\";s:0:\"\";s:8:\"password\";s:0:\"\";s:13:\"sha1_password\";s:40:\"ff771755774e0b4a40c05102c24eb0d8f656e609\";s:15:\"organization_id\";s:3:\"105\";s:6:\"active\";s:1:\"1\";s:14:\"affiliate_code\";s:0:\"\";s:18:\"affiliate_ptd_code\";s:0:\"\";s:15:\"affiliate_level\";s:0:\"\";s:21:\"affiliate_parent_code\";s:0:\"\";s:10:\"last_login\";s:1:\"0\";s:6:\"logins\";s:1:\"0\";s:20:\"affiliate_first_name\";s:0:\"\";s:19:\"affiliate_last_name\";s:0:\"\";s:16:\"affiliate_street\";s:0:\"\";s:18:\"affiliate_street_2\";s:0:\"\";s:14:\"affiliate_city\";s:0:\"\";s:15:\"affiliate_state\";s:0:\"\";s:13:\"affiliate_zip\";s:0:\"\";s:13:\"affiliate_tax\";s:0:\"\";s:17:\"organization_name\";s:0:\"\";}}');
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
INSERT INTO `failed_logins` VALUES ('morris@nerd.co.ke','127.0.0.1',1302704135),('morris@nerd.co.ke','127.0.0.1',1302704135);
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
) ENGINE=InnoDB AUTO_INCREMENT=2801 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,1272670664,'KSU','0',1,8,0,0,0,0,1,1);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2011 at 04:40 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `paper_tiger`
--

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='0 for any of the max means unlimited.' AUTO_INCREMENT=18 ;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `level`, `name`, `plan_description`, `mailing_list`, `price`, `billing_cycle`, `groups`, `active`, `max_databases`, `max_users`, `allow_extra_users`, `max_locations`, `max_categories`, `max_items`, `allow_reports`, `allow_backups`, `allow_ssl`, `allow_db_import`, `labels`, `allow_transfers`, `advanced_search`, `find_first`, `active_msg`) VALUES
(1, 1, 'Free', 'Free', 'Paper Tiger Online Free', 0.00, 'month', 0, 1, 1, 1, 0, 1, -1, 100, 0, 1, 1, 0, 0, 0, 0, 0, 6),
(2, 4, 'Pro', 'Pro', 'Paper Tiger Online Pro', 9.00, 'month', 1, 1, -1, 1, 0, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(3, 5, 'ProYearly', 'Pro Yearly', 'Paper Tiger Online Pro', 90.00, 'year', 1, 1, -1, 1, 0, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 5),
(4, 0, 'Beta', 'Beta Tester', 'Paper Tiger Online Free', 0.00, 'month', 98, 1, -1, 1, 0, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
(5, 7, 'Business', 'Business', 'Paper Tiger Online Business', 19.00, 'month', 2, 1, -1, 10, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(6, 8, 'BusinessYearly', 'BusinessYearly', 'Paper Tiger Online Business', 190.00, 'year', 2, 1, -1, 10, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(7, 13, 'Unlimited', 'Unlimited', 'Paper Tiger Online Unlimited', 59.00, 'month', 3, 1, -1, -1, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(8, 14, 'UnlimitedYearly', 'UnlimitedYearly', 'Paper Tiger Online Unlimited', 590.00, 'year', 3, 1, -1, -1, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(9, 2, 'Basic', 'Basic', 'Paper Tiger Online Basic', 5.00, 'month', 4, 1, 1, 1, 0, 2, -1, -1, 1, 1, 1, 0, 1, 1, 0, 1, 6),
(10, 3, 'BasicYearly', 'BasicYearly', 'Paper Tiger Online Basic', 50.00, 'year', 4, 1, 1, 1, 0, 2, -1, -1, 1, 1, 1, 0, 1, 1, 0, 1, 6),
(11, 10, 'Elite', 'Elite', 'Paper Tiger Online Elite', 39.00, 'month', 5, 1, -1, 30, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(12, 11, 'EliteYearly', 'EliteYearly', 'Paper Tiger Online Elite', 390.00, 'year', 5, 1, -1, 30, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(13, 6, 'ProYearlySpecial', 'Pro Special', 'Paper Tiger Online Pro', 75.60, 'year', 6, 1, -1, 1, 0, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(14, 99, 'FreeYearly', 'Free Pro Yearly', 'Paper Tiger Online Free', 0.00, 'year', 97, 1, -1, 1, 0, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(15, 9, 'BusinessYearlySpecial', 'Business Special', 'Paper Tiger Online Business', 159.60, 'year', 6, 1, -1, 10, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(16, 12, 'EliteYearlySpecial', 'Elite Special', 'Paper Tiger Online Elite', 327.60, 'year', 6, 1, -1, 30, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6),
(17, 15, 'UnlimitedYearlySpecial', 'Unlimited Special', 'Paper Tiger Online Unlimited', 495.60, 'year', 6, 1, -1, -1, 1, -1, -1, -1, 1, 1, 1, 1, 1, 1, 1, 1, 6);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'4da5b89aad03d',1272670664,1284953579,'8ee95d7bfa10bc10a9ce47c6c4015da2','Morris','Mwanga','morris@nerd.co.ke',NULL,'','ff771755774e0b4a40c05102c24eb0d8f656e609',105,1,NULL,NULL,NULL,NULL,1302706330,1,'','','','','','','','');
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

-- Dump completed on 2011-04-13 11:03:37
