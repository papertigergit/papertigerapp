-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2011 at 10:05 AM
-- Server version: 5.0.77
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `digital_tiger`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_levels`
--

CREATE TABLE IF NOT EXISTS `affiliate_levels` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `percentage` decimal(2,2) NOT NULL,
  `recurring` int(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `affiliate_levels`
--

INSERT INTO `affiliate_levels` (`id`, `name`, `percentage`, `recurring`) VALUES
(1, 'Affiliate', 0.15, 0),
(2, 'Reseller', 0.30, 1),
(3, 'Expert', 0.30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_payments`
--

CREATE TABLE IF NOT EXISTS `affiliate_payments` (
  `id` int(11) NOT NULL auto_increment,
  `affiliate_code` varchar(5) NOT NULL,
  `date` int(11) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `affiliate_payments`
--


-- --------------------------------------------------------

--
-- Table structure for table `affiliate_tracking`
--

CREATE TABLE IF NOT EXISTS `affiliate_tracking` (
  `id` int(11) NOT NULL auto_increment,
  `is_active` tinyint(1) NOT NULL,
  `organization_id` int(11) NOT NULL COMMENT 'Organization that signed up under the affiliate',
  `affiliate_code` varchar(5) NOT NULL COMMENT 'The affiliate that referred the user with user_id',
  `original_date` int(11) NOT NULL,
  `start_date` int(11) NOT NULL,
  `last_payment_date` int(11) NOT NULL,
  `initial_payment_made` int(1) default '0',
  `tier` int(1) NOT NULL default '0',
  `tier_child` varchar(5) default NULL,
  PRIMARY KEY  (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `affiliate_code` (`affiliate_code`),
  KEY `start_date` (`start_date`),
  KEY `initial_payment_made` (`initial_payment_made`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `affiliate_tracking`
--


-- --------------------------------------------------------

--
-- Table structure for table `authorized_devices`
--

CREATE TABLE IF NOT EXISTS `authorized_devices` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `device_hash` varchar(32) default NULL,
  `date_created` int(11) default NULL,
  `date_last_access` int(11) default NULL,
  `client_version` varchar(4) default NULL,
  `os_string` varchar(60) default NULL,
  `status` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `authorized_devices`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_auth_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_auth_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `device_hash` varchar(32) default NULL,
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ci_auth_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('5e4dfbd748ae19cfacbc2a0780eaee3a', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480609, ''),
('60d43890bd4d928fe9f5c759d2846a6e', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480624, ''),
('9b0b4eef21cdb8fccee68dc327068561', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480639, ''),
('eeb623e164490b0897ff560f60be9267', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480654, ''),
('cd7312b626bd8d0bc29b9a8985db42a4', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480669, ''),
('45320c943937b8788146d9a09f688ae7', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480594, ''),
('0c401db3d8180d902d0f575dac69aeea', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480579, ''),
('58c2a5766d18ca2f32b078cdbd1702ff', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480564, ''),
('d2d11acc0523c3ab30ddd67092523301', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480549, ''),
('97ef6cd2add49e872fc475f1c1ddfcf2', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480533, ''),
('33662d725b464848cb7db6a81b5e8d87', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480518, ''),
('1302e5ae2ed4ccc9da0a12bd70050925', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480503, ''),
('8cae9c6422618afd616e4d8f37866f8e', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480488, ''),
('8570938a0f60a9c7dcfe9265b471acae', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480413, ''),
('b5c01b5ed8e09fb51bbf5b0440aaa55b', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480473, ''),
('b77c45fd588bbe60bda6c144d3770139', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480458, ''),
('ea467d1e31656076307b6a79a03e9aea', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480443, ''),
('00ec0b6f5bc9665601e9dcaab51ab1d8', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480428, ''),
('5af56efb1dbe089472d15c577cad74fa', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480398, ''),
('676302aedff49e7deeaf407928509181', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480383, ''),
('90b0e7b1f65a26c794fe01bf4b2fe806', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480368, ''),
('391594f34724a36fd2f3388a4a40d6cb', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480353, ''),
('7d52aad69ecdf278204234bcf205972d', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480338, ''),
('8054eeb8a680fda4f7d6c5dee999509c', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480293, ''),
('b616bd75f085baaa36eaf65343831fda', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480308, ''),
('65fb8418a1e92e0c1edd3897f49da848', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480323, ''),
('0fb240f84b993250a84aaf4c7b65e811', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480278, ''),
('a140db5acce5e9fb67521cc65c71f13c', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480262, ''),
('74359e09b3bfee3577dac0b05e3bb350', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481121, ''),
('da05401a0fb76203f2db5b0aa446543f', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481136, ''),
('0e112f28c99a41fe83b0b6028c4105bb', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480247, ''),
('b47f467f398a92b04b76b0a92550f81d', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480232, ''),
('b53eb55c9eb96748e8d948a87c03541a', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480217, ''),
('4765fd8384c9b0ed5d994627a1171a20', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480202, ''),
('5417d27d7d91857719c0c7b2cc9bb081', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480187, ''),
('48595196b644e54023764531e576544d', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480143, ''),
('cfc90498e16ecb3e14be4c2a4b520a30', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480128, ''),
('0dbe3c1873f176f18b485cbf05fd17da', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480113, ''),
('cc28e0a0a12f81e64f614cc28d09b156', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480098, ''),
('b7505016af8702717903c4b11a2b24ee', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481106, ''),
('f88030602dbd48b329fe0d8f12fa1ecc', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481090, ''),
('8f2820ced347f93bf5ffa845eaa7cab2', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481075, ''),
('f69e5d2e0402deb514caf8378d60a641', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480083, ''),
('34a2bf5904a5b8c5472e15e29bc40a5c', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480068, ''),
('0742bdacd8e5957361981881ef1bc548', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480053, ''),
('b7f72c649c2857403b06f46b94ff6422', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480011, ''),
('38b5bdd8d1d1f070a997e9e6bbc59059', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479996, ''),
('db6ed7fd9de7ab39219cd8f08edc080b', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479981, ''),
('c9d8228960dd0f6745cd22e712826e34', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479966, ''),
('e61d006a4363a12a4f8a333c97e5535f', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479950, ''),
('244ccb579f2fa2fcf346e862e938b315', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481060, ''),
('f1250b572b61e0640431bb520d8ed9bd', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481045, ''),
('db22dd034ff7e5892de886a1e38b3cf2', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481030, ''),
('8f0838122ddaac890141a6d3a728da7b', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479935, ''),
('1d9dc127ca028c9e6efac7da3893d9cb', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481015, ''),
('1bb13a6f3228bb601b95985ac2354153', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303481000, ''),
('1c8169564cc8c18a7d22d9635a5b0799', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480985, ''),
('db2b482bb4629d70de400b564783a491', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480970, ''),
('c21462fc6a71b6a08d603c4df042bd7a', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480955, ''),
('f77bb0e8fdec2fe68af96e9be30efaa4', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480940, ''),
('52d5b3fb2132d83c5dd1fe5884815964', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480925, ''),
('c790306b9ee0f7179d7dc4a7ef299636', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480910, ''),
('7057ccd5696841824acf9b3997d63c4f', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480895, ''),
('b9b5d9cd3d7095deee71aa5c9293c902', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480880, ''),
('28a194982ba45d876116d972cb6d690c', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480865, ''),
('ffdbc405ce4039829d5cd51eedd52b71', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480850, ''),
('3e3eccb8ff9a19678fc56fe52a6dc805', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480835, ''),
('69e48807c0b10bf18ed6709bd35d76f2', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480819, ''),
('db1077c43f59569aea487faef35c3ae2', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480804, ''),
('e965669df3fd5db073e232e299df6faa', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480789, ''),
('64aa8f69788f732ad8443697e31d6266', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480774, ''),
('4cad7ce344e6b7055819b82d2d0f835b', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479920, ''),
('20b9345b3b1e56f0a205e040940d14fb', '104.6.6.17', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; ', 1303480465, 'a:2:{s:13:"authenticated";s:4:"true";s:9:"user_data";a:28:{s:2:"id";s:1:"2";s:10:"session_id";s:13:"4db1867185aa7";s:10:"date_added";s:10:"1303231596";s:14:"date_confirmed";s:1:"0";s:15:"activation_code";s:0:"";s:10:"first_name";s:8:"Federico";s:9:"last_name";s:7:"Holgado";s:13:"email_address";s:18:"fholgado@gmail.com";s:17:"new_email_address";s:0:"";s:8:"password";s:0:"";s:13:"sha1_password";s:40:"9b82049a8bde02ad974b5010c7fa29993186002d";s:15:"organization_id";s:4:"2801";s:6:"active";s:1:"1";s:14:"affiliate_code";s:0:"";s:18:"affiliate_ptd_code";s:0:"";s:15:"affiliate_level";s:0:"";s:21:"affiliate_parent_code";s:0:"";s:10:"last_login";s:10:"1303401273";s:6:"logins";s:1:"5";s:20:"affiliate_first_name";s:0:"";s:19:"affiliate_last_name";s:0:"";s:16:"affiliate_street";s:0:"";s:18:"affiliate_street_2";s:0:"";s:14:"affiliate_city";s:0:"";s:15:"affiliate_state";s:0:"";s:13:"affiliate_zip";s:0:"";s:13:"affiliate_tax";s:0:"";s:17:"organization_name";s:10:"Monticello";}}'),
('aa93ec58a0bf4c518420dfbf4dee6718', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479905, ''),
('db4d70c088ff04326858b20ff90a05ac', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479890, ''),
('8b310a96fe18b26920d77a99525616cd', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479875, ''),
('1fb4d76c5edb666594aa33c27c5855c8', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479860, ''),
('beca2855d7a4cb96ffa98a4628820fc4', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479845, ''),
('13b29a31759c1b3892acd7ccd13a37c9', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479830, ''),
('1f328ef661123d6f2d05256d28f1679a', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303479788, ''),
('9238d4df32c556fb5ec35c6c52201eb0', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480744, ''),
('5c2eb917264bab944614e703e4eec4f0', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480759, ''),
('5eb3f00516857e4ba1fcabefa22cc565', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480714, ''),
('e771033d09b68977ad9ebc5518e39866', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480729, ''),
('7af910c8d670e8e9aeae034832bdd597', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480699, ''),
('a51aea3ff9ea0226f0417d35fc9d9df0', '127.0.0.1', 'curl/7.15.5 (i686-redhat-linux-gnu) libcurl/7.15.5', 1303480684, '');

-- --------------------------------------------------------

--
-- Table structure for table `digital_files`
--

CREATE TABLE IF NOT EXISTS `digital_files` (
  `id` int(11) NOT NULL auto_increment,
  `file_hash` varchar(32) NOT NULL,
  `encrypted_file_hash` varchar(32) default NULL,
  `original_file_name` varchar(255) NOT NULL,
  `original_file_path` varchar(255) default NULL,
  `indexing_status` int(2) default NULL,
  `organization_id` int(11) default NULL,
  `file_id` int(11) default NULL,
  `revision_id` int(11) default NULL,
  `upload_time` int(11) default NULL,
  `uploaded_by` int(11) default NULL,
  `upload_source` int(2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `indexing_status` (`indexing_status`),
  KEY `upload_source` (`upload_source`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `digital_files`
--

INSERT INTO `digital_files` (`id`, `file_hash`, `encrypted_file_hash`, `original_file_name`, `original_file_path`, `indexing_status`, `organization_id`, `file_id`, `revision_id`, `upload_time`, `uploaded_by`, `upload_source`) VALUES
(1, '75e773ef5ee240e65072fd311eabd439', NULL, 'What_does_The_Monticello_Corporation_Offer_You.docx', '/var/www/html/uploads/What_does_The_Monticello_Corporation_Offer_You.docx', 0, 2801, 1, 0, 1303232080, 2, 1),
(2, 'd8a581f6904038d7d7cc337a71689f1b', NULL, 'EvacuationGuide.doc', '/var/www/html/uploads/EvacuationGuide.doc', 0, 105, 2, 0, 1303232088, 1, 1),
(3, 'e59cbdff931ee7537cae6bcab2e5a3ac', NULL, 'what_do_i_need.pdf', '/var/www/html/uploads/what_do_i_need.pdf', 0, 2801, 3, 0, 1303232407, 2, 1),
(4, '2b338d0a5bec0347085c160357efa75e', NULL, 'lorem_ipsum.odt', '/var/www/html/uploads/lorem_ipsum1.odt', 0, 105, 4, 0, 1303237295, 1, 1),
(5, '580a806131023da6f31f9c52fd0b4e5a', NULL, 'To-do:Projects-Janets.docx', '/var/www/html/uploads/To-do:Projects-Janets.docx', 0, 2801, 5, 0, 1303237311, 2, 1),
(6, 'c4ad99dae1880e48ad83807f6b211ccd', NULL, 'word.pdf', '/var/www/html/uploads/word.pdf', 0, 105, 6, 0, 1303237646, 1, 1),
(7, 'e81f770779dd45818a043ba4dbfc2f08', NULL, 'payments_20110412-20110414.xls', '/var/www/html/uploads/payments_20110412-20110414.xls', 0, 2801, 7, 0, 1303240879, 2, 1),
(8, 'ab4110ad61ec5a733b7bf97012f4eeae', NULL, 'Screen_shot_2011-03-08_at_5.47_.52_PM_.png', '/var/www/html/uploads/Screen_shot_2011-03-08_at_5.47_.52_PM_.png', 0, 2801, 8, 0, 1303241371, 2, 1),
(9, 'b313f0301b656b5cafcb6fe0afbf83f3', NULL, 'TAGS.pdf', '/var/www/html/uploads/TAGS1.pdf', 0, 2801, 9, 0, 1303241669, 2, 1),
(10, 'b313f0301b656b5cafcb6fe0afbf83f3', NULL, 'TAGS.pdf', '/var/www/html/uploads/TAGS2.pdf', 0, 2801, 10, 0, 1303243493, 2, 1),
(24, '28fa0e242b6a71bae143dc30a4bd58f8', NULL, 'Automatic_Archive_Changes.doc', '/var/www/html/uploads/Automatic_Archive_Changes.doc', 0, 2801, 24, 0, 1303480133, 2, 1),
(23, '72f5538b5a022ef31946c45558e5af80', NULL, '2-16_QAs.docx', '/var/www/html/uploads/2-16_QAs.docx', 0, 2801, 23, 0, 1303480006, 2, 1),
(22, '9ec044d426eab0baf4ba398788d69f16', NULL, 'email_draft_for_orders.docx', '/var/www/html/uploads/email_draft_for_orders.docx', 0, 2801, 22, 0, 1303401297, 2, 1),
(20, 'd8a581f6904038d7d7cc337a71689f1b', NULL, 'EvacuationGuide.doc', '/var/www/html/uploads/EvacuationGuide2.doc', 0, 105, 20, 0, 1303322745, 1, 1),
(21, '0cec1a201f1c832cbd573421e673525d', NULL, 'Intuit_fax.docx', '/var/www/html/uploads/Intuit_fax.docx', 0, 2801, 21, 0, 1303389574, 2, 1),
(19, 'd8a581f6904038d7d7cc337a71689f1b', NULL, 'EvacuationGuide.doc', '/var/www/html/uploads/EvacuationGuide1.doc', 0, 105, 19, 0, 1303322196, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `digital_file_keys`
--

CREATE TABLE IF NOT EXISTS `digital_file_keys` (
  `id` int(11) NOT NULL auto_increment,
  `file_type` varchar(60) default NULL,
  `file_extension` varchar(32) default NULL,
  `file_size` int(11) NOT NULL,
  `date_created` int(11) default NULL,
  `date_modified` int(11) default NULL,
  `s3_key` varchar(32) default NULL,
  `thumb_small` tinyint(4) default NULL,
  `thumb_large` tinyint(4) default NULL,
  `indexing_status` int(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `digital_file_keys`
--

INSERT INTO `digital_file_keys` (`id`, `file_type`, `file_extension`, `file_size`, `date_created`, `date_modified`, `s3_key`, `thumb_small`, `thumb_large`, `indexing_status`) VALUES
(1, 'application/vnd.openxmlformats-officedocument.wordprocessing', '.docx', 131, 1303232079, 0, '00028010000000130323207933823318', 0, 0, 1),
(2, 'application/msword', '.doc', 1958, 1303232087, 0, '00001050000000130323208716131757', 0, 0, 1),
(3, 'application/pdf', '.pdf', 10, 1303232406, 0, '00028010000000130323240685951457', 0, 0, 1),
(4, 'application/vnd.oasis.opendocument.text', '.odt', 16, 1303237294, 0, '00001050000000130323729588278471', 0, 0, 1),
(5, 'application/vnd.openxmlformats-officedocument.wordprocessing', '.docx', 129, 1303237310, 0, '00028010000000130323731016034995', 0, 0, 1),
(6, 'application/pdf', '.pdf', 196, 1303237645, 0, '00001050000000130323764549825583', 0, 0, 1),
(7, 'application/vnd.ms-excel', '.xls', 31, 1303240878, 0, '00028010000000130324087847737691', 0, 0, 1),
(8, 'image/png', '.png', 400, 1303241370, 0, '00028010000000130324137023732837', 0, 0, 1),
(9, 'application/pdf', '.pdf', 354, 1303241669, 0, '00028010000000130324166924747621', 0, 0, 1),
(10, 'application/pdf', '.pdf', 354, 1303243493, 0, '00028010000000130324349364881463', 0, 0, 1),
(23, 'application/vnd.openxmlformats-officedocument.wordprocessing', '.docx', 151, 1303480006, 0, '00028010000000130348000689955196', 0, 0, 1),
(22, 'application/vnd.openxmlformats-officedocument.wordprocessing', '.docx', 77, 1303401296, 0, '00028010000000130340129672829755', 0, 0, 1),
(21, 'application/vnd.openxmlformats-officedocument.wordprocessing', '.docx', 23, 1303389574, 0, '00028010000000130338957466418877', 0, 0, 1),
(20, 'application/msword', '.doc', 1958, 1303322744, 0, '00001050000000130332274434515005', 0, 0, 1),
(19, 'application/msword', '.doc', 1958, 1303322195, 0, '00001050000000130332219599541939', 0, 0, 1),
(24, 'application/msword', '.doc', 1494, 1303480133, 0, '00028010000000130348013341258600', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `digital_file_status`
--

CREATE TABLE IF NOT EXISTS `digital_file_status` (
  `id` int(11) NOT NULL auto_increment,
  `file_status_id` int(11) NOT NULL,
  `file_status_description` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `digital_file_status`
--

INSERT INTO `digital_file_status` (`id`, `file_status_id`, `file_status_description`) VALUES
(1, 1, 'Sucessfully Indexed'),
(2, 2, 'Indexing in Progress '),
(3, 0, 'Pending Indexing'),
(4, 3, 'Pending Upload');

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `email_address` varchar(60) default NULL,
  `ip_address` varchar(20) NOT NULL,
  `date` int(11) NOT NULL,
  KEY `ip_address` (`ip_address`),
  KEY `user_id` (`email_address`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `failed_logins`
--

INSERT INTO `failed_logins` (`email_address`, `ip_address`, `date`) VALUES
('morris@nerd.co.ke', '127.0.0.1', 1302704135),
('morris@nerd.co.ke', '127.0.0.1', 1302704135),
('nkaronji@thepapertiger.com', '104.6.6.17', 1303305308),
('nkaronji@thepapertiger.com', '104.6.6.17', 1303305308),
('nkaronji@thepapertiger.com', '104.6.6.17', 1303305801),
('nkaronji@thepapertiger.com', '104.6.6.17', 1303305801),
('fholgado@gmail.com', '104.6.6.17', 1303389464),
('fholgado@gmail.com', '104.6.6.17', 1303389464);

-- --------------------------------------------------------

--
-- Table structure for table `file_labels`
--

CREATE TABLE IF NOT EXISTS `file_labels` (
  `id` int(11) NOT NULL auto_increment,
  `label_id` int(11) default NULL,
  `file_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `label_id` (`label_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file_labels`
--


-- --------------------------------------------------------

--
-- Table structure for table `file_upload_requests`
--

CREATE TABLE IF NOT EXISTS `file_upload_requests` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(60) default NULL,
  `file_hash` varchar(32) default NULL,
  `file_path` varchar(60) default NULL,
  `ip_address` varchar(20) default NULL,
  `time_stamp` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file_upload_requests`
--


-- --------------------------------------------------------

--
-- Table structure for table `incoming_api_sessions`
--

CREATE TABLE IF NOT EXISTS `incoming_api_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(60) default NULL,
  `password` varchar(60) default NULL,
  `client_hash` varchar(32) default NULL,
  `client_version` varchar(4) default NULL,
  `computer_name` varchar(60) default NULL,
  `os_string` varchar(60) default NULL,
  `action` varchar(15) default NULL,
  `remote_addr` varchar(19) default NULL,
  `time_stamp` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `incoming_api_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE IF NOT EXISTS `labels` (
  `id` int(11) NOT NULL auto_increment,
  `label_name` varchar(255) default NULL,
  `label_color` varchar(6) default NULL,
  `font_color` varchar(6) default NULL,
  `label_notes` varchar(255) default NULL,
  `label_owner_id` int(11) default NULL,
  `label_org_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `labels`
--


-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL,
  `organization_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `type` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `organization_id` (`organization_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE IF NOT EXISTS `organizations` (
  `id` int(11) NOT NULL auto_increment,
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
  `newsletter` tinyint(4) default '1',
  PRIMARY KEY  (`id`),
  KEY `admin_user` (`admin_user`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2802 ;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `date_added`, `name`, `subscription_id`, `admin_user`, `package_id`, `is_canceled`, `is_suspended`, `suspended_date`, `extra_user_slots`, `force_ssl`, `newsletter`) VALUES
(1, 1272670664, 'KSU', '0', 1, 8, 0, 0, 0, 0, 1, 1),
(2801, 1303231596, 'Monticello', '0', 2, 1, 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL auto_increment,
  `level` int(11) NOT NULL COMMENT 'definitive what package is better that which. need a simple number to use for upgrade/downgrade',
  `name` varchar(40) NOT NULL,
  `plan_description` varchar(60) NOT NULL COMMENT 'Description field for plans',
  `mailing_list` varchar(30) default NULL,
  `price` decimal(5,2) NOT NULL,
  `billing_cycle` varchar(5) default NULL,
  `groups` tinyint(4) default NULL,
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
  `active_msg` int(11) default NULL,
  PRIMARY KEY  (`id`)
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

-- --------------------------------------------------------

--
-- Table structure for table `upload_source`
--

CREATE TABLE IF NOT EXISTS `upload_source` (
  `id` int(11) NOT NULL auto_increment,
  `upload_source_id` int(11) NOT NULL,
  `upload_source_description` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `upload_source`
--

INSERT INTO `upload_source` (`id`, `upload_source_id`, `upload_source_description`) VALUES
(1, 1, 'Web Uploader'),
(2, 2, 'Desktop Uploader'),
(3, 3, 'Email Attachment');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `session_id` varchar(50) default NULL,
  `date_added` int(11) NOT NULL,
  `date_confirmed` int(11) NOT NULL,
  `activation_code` varchar(255) default NULL,
  `first_name` varchar(255) default NULL,
  `last_name` varchar(255) default NULL,
  `email_address` varchar(60) NOT NULL,
  `new_email_address` varchar(60) default NULL COMMENT 'Keep Users new email address pendng here until the confirmation is successful',
  `password` text NOT NULL,
  `sha1_password` varchar(60) default NULL COMMENT 'salt=7D9290F21C5BFBC99339C465D79567BB',
  `organization_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `affiliate_code` varchar(5) default NULL,
  `affiliate_ptd_code` varchar(6) default NULL,
  `affiliate_level` int(2) default NULL COMMENT '1 - affiliate 15%, 2 - reseller 30%',
  `affiliate_parent_code` varchar(5) default NULL,
  `last_login` int(11) default '0',
  `logins` int(11) default '0',
  `affiliate_first_name` varchar(255) NOT NULL,
  `affiliate_last_name` varchar(255) NOT NULL,
  `affiliate_street` varchar(255) NOT NULL,
  `affiliate_street_2` varchar(255) NOT NULL,
  `affiliate_city` varchar(255) NOT NULL,
  `affiliate_state` varchar(255) NOT NULL,
  `affiliate_zip` varchar(5) NOT NULL,
  `affiliate_tax` varchar(100) NOT NULL COMMENT 'Encrypted with CI',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `affiliate_code` (`affiliate_code`),
  KEY `organization_id` (`organization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `session_id`, `date_added`, `date_confirmed`, `activation_code`, `first_name`, `last_name`, `email_address`, `new_email_address`, `password`, `sha1_password`, `organization_id`, `active`, `affiliate_code`, `affiliate_ptd_code`, `affiliate_level`, `affiliate_parent_code`, `last_login`, `logins`, `affiliate_first_name`, `affiliate_last_name`, `affiliate_street`, `affiliate_street_2`, `affiliate_city`, `affiliate_state`, `affiliate_zip`, `affiliate_tax`) VALUES
(1, '4daf1c0d73649', 1272670664, 1284953579, '8ee95d7bfa10bc10a9ce47c6c4015da2', 'Morris', 'Mwanga', 'morris@nerd.co.ke', NULL, '', 'ff771755774e0b4a40c05102c24eb0d8f656e609', 105, 1, NULL, NULL, NULL, NULL, 1303321613, 6, '', '', '', '', '', '', '', ''),
(2, '4db1867185aa7', 1303231596, 0, NULL, 'Federico', 'Holgado', 'fholgado@gmail.com', NULL, '', '9b82049a8bde02ad974b5010c7fa29993186002d', 2801, 1, NULL, NULL, NULL, NULL, 1303479921, 6, '', '', '', '', '', '', '', ''),
(3, '4daeee6d1e941', 1273247256, 1282143893, NULL, 'Nicholas', 'Karonji', 'nkaronji@thepapertiger.com', NULL, '', '687cfb6f9064c449579b1220ca8fbe445743703a', 204, 1, NULL, NULL, NULL, NULL, 1303309933, 1, '', '', '', '', '', '', '', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_ibfk_1` FOREIGN KEY (`admin_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `organizations_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;
