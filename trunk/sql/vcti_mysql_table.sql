-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 24, 2008 at 07:57 PM
-- Server version: 5.0.58
-- PHP Version: 5.1.6
-- 
-- Database: `vcti`
-- 

-- --------------------------------------------------------

USE `vcti`;

-- 
-- Table structure for table `cti_calls`
-- 

DROP TABLE IF EXISTS `cti_calls`;
CREATE TABLE `cti_calls` (
  `call_uuid` char(32) collate utf8_unicode_ci NOT NULL default '',
  `session_uuid` char(32) collate utf8_unicode_ci NOT NULL default '',
  `begin_timestamp` timestamp NULL default NULL,
  `end_timestamp` timestamp NULL default NULL,
  `destination` char(32) collate utf8_unicode_ci NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `type` int(11) default NULL,
  PRIMARY KEY  (`call_uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `cti_calls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cti_config`
-- 

DROP TABLE IF EXISTS `cti_config`;
CREATE TABLE `cti_config` (
  `chiave` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `valore` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `titolo` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `descrizione` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`chiave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `cti_config`
-- 

INSERT INTO `cti_config` VALUES ('pass', 'voiper', 'Manager Password', 'Manager Password (voiper)');
INSERT INTO `cti_config` VALUES ('host', 'localhost', 'Manager Server', 'Manager Server');
INSERT INTO `cti_config` VALUES ('port', '5038', 'Manager Port', 'Manager Port');
INSERT INTO `cti_config` VALUES ('recbitrate', '96', 'Record BitRate', 'Record BitRate');
INSERT INTO `cti_config` VALUES ('recpath', '/monitor/', 'Record Path', 'Record Path');
INSERT INTO `cti_config` VALUES ('outbound_prefix', '', 'Outbound Prefix', 'Outbound Prefix');
INSERT INTO `cti_config` VALUES ('parkmoh', 'default', 'MusicOnHold', 'MusicOnHold');
INSERT INTO `cti_config` VALUES ('user', 'admin', 'Manager User', 'Manager User');
INSERT INTO `cti_config` VALUES ('country_code', '+39', 'Country code', 'Country code');
INSERT INTO `cti_config` VALUES ('extension_pattern', '/^\\d{4}$/', 'Extension pattern', 'Regular expression used to match local extensions');

-- --------------------------------------------------------

-- 
-- Table structure for table `cti_sessions`
-- 

DROP TABLE IF EXISTS `cti_sessions`;
CREATE TABLE `cti_sessions` (
  `session_uuid` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `extension` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `status` int(11) NOT NULL default '0',
  `login` timestamp NULL default NULL,
  `logout` timestamp NULL default NULL,
  `lastupdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`session_uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `cti_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cti_users`
-- 

DROP TABLE IF EXISTS `cti_users`;
CREATE TABLE `cti_users` (
  `user_id` int(11) NOT NULL auto_increment,
  `username` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `password` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `trunk` int(1) NOT NULL default '-1',
  `login` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `permission` int(1) NOT NULL default '-1',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `cti_users`
-- 

INSERT INTO `cti_users` VALUES (1, 'Voiper Demo', 'voiper', -1, 'voiper', -1);
