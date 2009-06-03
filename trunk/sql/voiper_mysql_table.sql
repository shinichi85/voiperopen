-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 24, 2009 at 02:28 PM
-- Server version: 5.0.54
-- PHP Version: 5.1.6
-- 
-- Database: `asterisk`
-- 

USE `asterisk`;

-- --------------------------------------------------------

-- 
-- Table structure for table `Backup`
-- 

DROP TABLE IF EXISTS `Backup`;
CREATE TABLE `Backup` (
  `Name` varchar(50) collate utf8_unicode_ci default NULL,
  `Voicemail` varchar(50) collate utf8_unicode_ci default NULL,
  `Recordings` varchar(50) collate utf8_unicode_ci default NULL,
  `Configurations` varchar(50) collate utf8_unicode_ci default NULL,
  `CDR` varchar(55) collate utf8_unicode_ci default NULL,
  `FOP` varchar(50) collate utf8_unicode_ci default NULL,
  `Minutes` varchar(50) collate utf8_unicode_ci default NULL,
  `Hours` varchar(50) collate utf8_unicode_ci default NULL,
  `Days` varchar(50) collate utf8_unicode_ci default NULL,
  `Months` varchar(50) collate utf8_unicode_ci default NULL,
  `Weekdays` varchar(50) collate utf8_unicode_ci default NULL,
  `Command` varchar(200) collate utf8_unicode_ci default NULL,
  `Method` varchar(50) collate utf8_unicode_ci default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

-- 
-- Dumping data for table `Backup`
-- 

INSERT INTO `Backup` (`Name`, `Voicemail`, `Recordings`, `Configurations`, `CDR`, `FOP`, `Minutes`, `Hours`, `Days`, `Months`, `Weekdays`, `Command`, `Method`, `ID`) VALUES ('Voiper-BAK', 'yes', 'yes', 'yes', 'yes', 'yes', ':0:', ':5:', '*', '*', '*', '0 5 * * * /var/lib/asterisk/bin/ampbackup.pl', 'follow_schedule', 42);

-- --------------------------------------------------------

-- 
-- Table structure for table `admin`
-- 

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `variable` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `value` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `admin`
-- 

INSERT INTO `admin` (`variable`, `value`) VALUES ('need_reload', 'false');
INSERT INTO `admin` (`variable`, `value`) VALUES ('version', '1.10.008');

-- --------------------------------------------------------

-- 
-- Table structure for table `ampusers`
-- 

DROP TABLE IF EXISTS `ampusers`;
CREATE TABLE `ampusers` (
  `username` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `password` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `extension_low` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `extension_high` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `deptname` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `sections` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `ampusers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `backupftp`
-- 

DROP TABLE IF EXISTS `backupftp`;
CREATE TABLE `backupftp` (
  `id` int(11) NOT NULL auto_increment,
  `ftpbackup` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `ftpuser` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `ftppassword` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `ftpsubdir` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `ftpserver` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `ftpemail` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `backupftp`
-- 

INSERT INTO `backupftp` (`id`, `ftpbackup`, `ftpuser`, `ftppassword`, `ftpsubdir`, `ftpserver`, `ftpemail`) VALUES (1, 'no', 'voiper', 'voiper', 'backup', '192.168.0.20', 'root');

-- --------------------------------------------------------

-- 
-- Table structure for table `customerdb`
-- 

DROP TABLE IF EXISTS `customerdb`;
CREATE TABLE `customerdb` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(45) collate utf8_unicode_ci NOT NULL default '',
  `addr1` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `addr2` varchar(150) collate utf8_unicode_ci default NULL,
  `city` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `state` varchar(5) collate utf8_unicode_ci NOT NULL default '',
  `zip` varchar(12) collate utf8_unicode_ci default NULL,
  `sip` varchar(20) collate utf8_unicode_ci default NULL,
  `did` varchar(45) collate utf8_unicode_ci default NULL,
  `device` varchar(50) collate utf8_unicode_ci default NULL,
  `ip` varchar(20) collate utf8_unicode_ci default NULL,
  `serial` varchar(50) collate utf8_unicode_ci default NULL,
  `account` varchar(6) collate utf8_unicode_ci default NULL,
  `email` varchar(150) collate utf8_unicode_ci default NULL,
  `username` varchar(25) collate utf8_unicode_ci default NULL,
  `password` varchar(25) collate utf8_unicode_ci default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `customerdb`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ddns`
-- 

DROP TABLE IF EXISTS `ddns`;
CREATE TABLE `ddns` (
  `variable` varchar(20) collate utf8_unicode_ci default NULL,
  `value` varchar(60) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `ddns`
-- 

INSERT INTO `ddns` (`variable`, `value`) VALUES ('daemon', '3600');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('syslog', 'yes');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('protocol', 'dyndns2');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('use', 'web, web=checkip.dyndns.org/, web-skip=''IP Address''');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('server', 'members.dyndns.org');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('login', 'mylogin');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('password', 'mypassword');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('host', 'myhost.dyndns.org');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('mail', 'lamiaemail@ilmiodominio.ext');
INSERT INTO `ddns` (`variable`, `value`) VALUES ('mail-failure', 'lamiaemail@ilmiodominio.ext');

-- --------------------------------------------------------

-- 
-- Table structure for table `extensions`
-- 

DROP TABLE IF EXISTS `extensions`;
CREATE TABLE `extensions` (
  `context` varchar(45) collate utf8_unicode_ci NOT NULL default 'default',
  `extension` varchar(45) collate utf8_unicode_ci NOT NULL default '',
  `priority` varchar(5) collate utf8_unicode_ci NOT NULL default '1',
  `application` varchar(45) collate utf8_unicode_ci NOT NULL default '',
  `args` varchar(255) collate utf8_unicode_ci default NULL,
  `descr` text collate utf8_unicode_ci,
  `flags` int(1) NOT NULL default '0',
  PRIMARY KEY  (`context`,`extension`,`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `extensions`
-- 

INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-local', '1000', '1', 'Macro', 'exten-vm,1000@default,1000', NULL, 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-local', '1001', '1', 'Macro', 'exten-vm,1001@default,1001', NULL, 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '4', 'Playback', 'custom/InternoDisponibile', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'hang', '2', 'Hangup', '', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'hang', '1', 'Playback', 'vm-goodbye', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-local', '1000', 'hint', 'SIP/1000', NULL, NULL, 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-local', '1001', 'hint', 'SIP/1001', NULL, NULL, 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 't', '2', 'Goto', 's,6', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 't', '1', 'Set', 'LOOPED=$[${LOOPED} + 1]', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('outrt-001-0Esterno', '_0X.', '02', 'Macro', 'outisbusy', 'No available circuits', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '09', 'Background', 'custom/aa_1', 'Chiamate in entrata da linea esterna Telecom', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '08', 'Set', 'TIMEOUT(response)=3', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '07', 'Set', 'TIMEOUT(digit)=3', 'incomingpstnchiuso', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '06', 'GotoIf', '$[${LOOPED} > 1]?hang,1', '1', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '05', 'Set', 'LOOPED=1', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '04', 'Wait', '1', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '03', 'Answer', '', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '02', 'GotoIf', '$["${DIALSTATUS}" = "ANSWER"]?4', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '01', 'GotoIf', '$["${DIALSTATUS}" = ""]?3', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'i', '2', 'Goto', 's,7', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'i', '1', 'Playback', 'invalid', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-group', '1', '2', 'Macro', 'vm,1000,DIRECTDIAL', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000**', '1', 'Macro', 'agent-del,2000', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000*', '1', 'Macro', 'agent-add,2000,1234', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '07', 'Set', 'TIMEOUT(digit)=3', 'incomingpstn', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '08', 'Set', 'TIMEOUT(response)=3', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'include', '2', 'app-messagecenter', '', '', 2);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'include', '1', 'ext-local', '', '', 2);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('outrt-001-0Esterno', '_0X.', '01', 'Macro', 'dialout-trunk,1,${EXTEN:1},,', NULL, 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '06', 'GotoIf', '$[${LOOPED} > 1]?hang,1', '1', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '04', 'Wait', '1', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '05', 'Set', 'LOOPED=1', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '5', 'Queue', '2000|t|||10', 'FromPots', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '3', 'Set', 'MONITOR_FILENAME=/var/spool/asterisk/monitor/${STRFTIME(${EPOCH},,%Y%m%d-%H%M%S)}-QUEUE${EXTEN}-${CALLERID(number)}-^-${UNIQUEID}', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-group', '1', '1', 'Macro', 'rg-group,ringall,20,,1000-1001,,,${DIAL_OPTIONS}', 'Demo 1', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '1', 'Answer', '', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '2', 'Set', 'CALLERID(number)=${CALLERID(num)}', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '03', 'Answer', '', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '02', 'GotoIf', '$["${DIALSTATUS}" = "ANSWER"]?4', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 's', '01', 'GotoIf', '$["${DIALSTATUS}" = ""]?3', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'i', '2', 'Goto', 's,7', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'i', '1', 'Playback', 'invalid', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 's', '09', 'Background', 'custom/aa_2', 'Chiamate in entrata da linea esterna Telecom', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 't', '1', 'Set', 'LOOPED=$[${LOOPED} + 1]', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 't', '2', 'Goto', 's,6', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'hang', '1', 'Playback', 'vm-goodbye', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_2', 'hang', '2', 'Hangup', '', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'include', '2', 'app-messagecenter', '', '', 2);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', 'include', '1', 'ext-local', '', '', 2);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('outbound-allroutes', 'include', '01', 'outrt-001-0Esterno', '', '', 2);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('aa_1', '1', '1', 'Goto', 'ext-queues,2000,1', '', 0);
INSERT INTO `extensions` (`context`, `extension`, `priority`, `application`, `args`, `descr`, `flags`) VALUES ('ext-queues', '2000', '6', 'Macro', 'vm,1000,DIRECTDIAL', '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `featureconfig`
-- 

DROP TABLE IF EXISTS `featureconfig`;
CREATE TABLE `featureconfig` (
  `id` int(11) NOT NULL auto_increment,
  `parkext` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `parkpos` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `context` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `parkingtime` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `transferdigittimeout` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `courtesytone` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pickupexten` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `xfersound` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `xferfailsound` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `featuredigittimeout` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `blindxfer` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `disconnect` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `automon` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `atxfer` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `adsipark` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `testfeature` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `featureconfig`
-- 

INSERT INTO `featureconfig` (`id`, `parkext`, `parkpos`, `context`, `parkingtime`, `transferdigittimeout`, `courtesytone`, `pickupexten`, `xfersound`, `xferfailsound`, `featuredigittimeout`, `blindxfer`, `disconnect`, `automon`, `atxfer`, `adsipark`, `testfeature`) VALUES (1, '70', '71-79', 'parkedcalls', '45', '3', 'beep', '*8', 'beep', 'beeperr', '500', '#', '*', '*1', '*2', 'yes', '*9,caller,Macro,apprecord');

-- --------------------------------------------------------

-- 
-- Table structure for table `fwmail`
-- 

DROP TABLE IF EXISTS `fwmail`;
CREATE TABLE `fwmail` (
  `variable` varchar(20) collate utf8_unicode_ci default NULL,
  `value` varchar(60) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fwmail`
-- 

INSERT INTO `fwmail` (`variable`, `value`) VALUES ('MailTo', 'lamiaemail@ilmiodominio.ext');

-- --------------------------------------------------------

-- 
-- Table structure for table `globals`
-- 

DROP TABLE IF EXISTS `globals`;
CREATE TABLE `globals` (
  `variable` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `value` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`variable`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `globals`
-- 

INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING', 'aa_2');
INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING_1', 'QUE-2000');
INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('AFTER_INCOMING_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('ALLOW_SIP_ANON', 'no');
INSERT INTO `globals` (`variable`, `value`) VALUES ('CALLBACKEXT_PASSWORD', '1234');
INSERT INTO `globals` (`variable`, `value`) VALUES ('CALLFILENAME', '""');
INSERT INTO `globals` (`variable`, `value`) VALUES ('CB_TRUNK', 'OUT_1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DAYNIGHT_PASSWORD', '1234');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIAL_OPTIONS', 'tr');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIAL_OPTIONS2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIAL_OUT', '9');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIALOUTIDS', '1/2/3/');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIRECTORY', 'disabled');
INSERT INTO `globals` (`variable`, `value`) VALUES ('DIRECTORY_OPTS', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('E1000', 'SIP');
INSERT INTO `globals` (`variable`, `value`) VALUES ('E1001', 'SIP');
INSERT INTO `globals` (`variable`, `value`) VALUES ('FAX', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('FAX_RX', 'disabled');
INSERT INTO `globals` (`variable`, `value`) VALUES ('FAX_RX_EMAIL', 'lamiaemail@ilmiodominio.ext');
INSERT INTO `globals` (`variable`, `value`) VALUES ('FAX_RX_EMAIL2', 'lamiaemail@ilmiodominio.ext');
INSERT INTO `globals` (`variable`, `value`) VALUES ('FAX_RX_FROM', 'lamiaemail@ilmiodominio.ext');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING', 'aa_2');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING_1', 'aa_2');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('HOLIDAY_INCOMING_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE', 'none');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE_1', 'forceafthours');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('IN_OVERRIDE_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING', 'aa_1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_1', 'QUE-2000');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC', 'Demo Incoming 1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC_1', 'Demo Incoming 2');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('INCOMING_DESC_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('MOH_COMMAND', '/usr/local/bin/madplay');
INSERT INTO `globals` (`variable`, `value`) VALUES ('MOH_VOLUME', '-1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('MONITOR_PASSWORD', '1234');
INSERT INTO `globals` (`variable`, `value`) VALUES ('NULL', '""');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OPERATOR', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUT_1', 'ZAP/g1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUT_2', 'ZAP/1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUT_3', 'ZAP/2');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTCID_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTCID_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTCID_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTMAXCHANS_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTMAXCHANS_2', '1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTMAXCHANS_3', '1');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTPREFIX_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTPREFIX_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTPREFIX_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTRIGHT_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTRIGHT_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTRIGHT_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('OUTTRUNKRIGHT_1', '0');
INSERT INTO `globals` (`variable`, `value`) VALUES ('PARKNOTIFY', 'SIP/200');
INSERT INTO `globals` (`variable`, `value`) VALUES ('RECORDEXTEN', '""');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS', 'mon-fri');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS_1', 'mon-fri');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS_2', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS_3', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS_4', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS_5', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS2_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS3_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGDAYS4_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS_1', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS_2', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS_3', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS_4', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS_5', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS2_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS3_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGMONTHS4_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME', '9:30-19:30');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME_1', '9:30-19:30');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME_2', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME_3', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME_4', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME_5', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME2_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME3_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4_1', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4_2', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4_3', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4_4', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('REGTIME4_5', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('RINGTIMER', '15');
INSERT INTO `globals` (`variable`, `value`) VALUES ('TRUNK_ALERT', 'sonar');
INSERT INTO `globals` (`variable`, `value`) VALUES ('TRUNKBUSY_ALERT', 'enabled');
INSERT INTO `globals` (`variable`, `value`) VALUES ('VM_DDTYPE', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('VM_GAIN', '5');
INSERT INTO `globals` (`variable`, `value`) VALUES ('VM_OPTS', '');
INSERT INTO `globals` (`variable`, `value`) VALUES ('VM_PREFIX', '*');
INSERT INTO `globals` (`variable`, `value`) VALUES ('WAV2MP3', 'disabled');
INSERT INTO `globals` (`variable`, `value`) VALUES ('ZAP_PASSWORD', '1234');

-- --------------------------------------------------------

-- 
-- Table structure for table `iax`
-- 

DROP TABLE IF EXISTS `iax`;
CREATE TABLE `iax` (
  `id` bigint(11) NOT NULL default '-1',
  `keyword` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `data` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `flags` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `iax`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `iaxconf`
-- 

DROP TABLE IF EXISTS `iaxconf`;
CREATE TABLE `iaxconf` (
  `id` int(11) NOT NULL default '0',
  `bindport` varchar(255) collate utf8_unicode_ci NOT NULL default '4569',
  `bindaddr` varchar(255) collate utf8_unicode_ci NOT NULL default '0.0.0.0',
  `disallow` varchar(255) collate utf8_unicode_ci NOT NULL default 'all',
  `allow` varchar(255) collate utf8_unicode_ci NOT NULL default 'alaw&ulaw&gsm&g729&ilbc&g726&h261&h263&h263p',
  `mailboxdetail` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `iaxcompat` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `delayreject` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `language` varchar(255) collate utf8_unicode_ci NOT NULL default 'it',
  `bandwidth` varchar(255) collate utf8_unicode_ci NOT NULL default 'medium',
  `jitterbuffer` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `tos` varchar(255) collate utf8_unicode_ci NOT NULL default 'none',
  `autokill` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `trunkfreq` varchar(255) collate utf8_unicode_ci NOT NULL default '20',
  `authdebug` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `amaflags` varchar(255) collate utf8_unicode_ci NOT NULL default 'default',
  `accountcode` varchar(255) collate utf8_unicode_ci NOT NULL default 'lss0101',
  `dropcount` varchar(255) collate utf8_unicode_ci NOT NULL default '2',
  `maxjitterbuffer` varchar(255) collate utf8_unicode_ci NOT NULL default '500',
  `maxexcessbuffer` varchar(255) collate utf8_unicode_ci NOT NULL default '80',
  `minexcessbuffer` varchar(255) collate utf8_unicode_ci NOT NULL default '10',
  `jittershrinkrate` varchar(255) collate utf8_unicode_ci NOT NULL default '1',
  `trunktimestamps` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `minregexpire` varchar(255) collate utf8_unicode_ci NOT NULL default '60',
  `maxregexpire` varchar(255) collate utf8_unicode_ci NOT NULL default '60',
  `iaxthreadcount` varchar(255) collate utf8_unicode_ci NOT NULL default '200',
  `iaxmaxthreadcount` varchar(255) collate utf8_unicode_ci NOT NULL default '1000',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `iaxconf`
-- 

INSERT INTO `iaxconf` (`id`, `bindport`, `bindaddr`, `disallow`, `allow`, `mailboxdetail`, `iaxcompat`, `delayreject`, `language`, `bandwidth`, `jitterbuffer`, `tos`, `autokill`, `trunkfreq`, `authdebug`, `amaflags`, `accountcode`, `dropcount`, `maxjitterbuffer`, `maxexcessbuffer`, `minexcessbuffer`, `jittershrinkrate`, `trunktimestamps`, `minregexpire`, `maxregexpire`, `iaxthreadcount`, `iaxmaxthreadcount`) VALUES (1, '4569', '0.0.0.0', 'all', 'alaw&ulaw&gsm&g729&ilbc&g726&h261&h263&h263p', 'yes', 'yes', 'yes', 'it', 'medium', 'no', 'none', 'no', '20', 'no', 'default', 'lss0101', '2', '500', '80', '10', '1', 'no', '60', '60', '200', '1000');

-- --------------------------------------------------------

-- 
-- Table structure for table `incoming`
-- 

DROP TABLE IF EXISTS `incoming`;
CREATE TABLE `incoming` (
  `cidnum` varchar(20) collate utf8_unicode_ci default NULL,
  `extension` varchar(20) collate utf8_unicode_ci default NULL,
  `destination` varchar(50) collate utf8_unicode_ci default NULL,
  `faxexten` varchar(20) collate utf8_unicode_ci default NULL,
  `faxemail` varchar(50) collate utf8_unicode_ci default NULL,
  `faxemail2` varchar(50) collate utf8_unicode_ci default NULL,
  `answer` tinyint(1) default NULL,
  `wait` int(2) default NULL,
  `privacyman` tinyint(1) default NULL,
  `CIDName` varchar(255) collate utf8_unicode_ci default NULL,
  `alertinfo` varchar(20) collate utf8_unicode_ci default NULL,
  `channel` varchar(20) collate utf8_unicode_ci default NULL,
  `ringing` varchar(20) collate utf8_unicode_ci default NULL,
  `ADDPrefix` varchar(255) collate utf8_unicode_ci default NULL,
  `phonebook` varchar(20) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `incoming`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `logwatch`
-- 

DROP TABLE IF EXISTS `logwatch`;
CREATE TABLE `logwatch` (
  `variable` varchar(20) collate utf8_unicode_ci default NULL,
  `value` varchar(60) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `logwatch`
-- 

INSERT INTO `logwatch` (`variable`, `value`) VALUES ('MailTo', 'lamiaemail@ilmiodominio.ext');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('LogDir', '/var/log');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('TmpDir', '/tmp');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('Print', 'No');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('UseMkTemp', 'Yes');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('MkTemp', '/bin/mktemp');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('Range', 'yesterday');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('Detail', 'High');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('Service', 'All');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('mailer', '/bin/mail');
INSERT INTO `logwatch` (`variable`, `value`) VALUES ('Archives', 'No');

-- --------------------------------------------------------

-- 
-- Table structure for table `manager`
-- 

DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `manager_id` int(11) NOT NULL auto_increment,
  `name` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `secret` varchar(50) collate utf8_unicode_ci default NULL,
  `deny` varchar(255) collate utf8_unicode_ci default NULL,
  `permit` varchar(255) collate utf8_unicode_ci default NULL,
  `read` varchar(50) collate utf8_unicode_ci default NULL,
  `write` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`manager_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `manager`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `meetme`
-- 

DROP TABLE IF EXISTS `meetme`;
CREATE TABLE `meetme` (
  `exten` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `options` varchar(15) collate utf8_unicode_ci default NULL,
  `userpin` varchar(50) collate utf8_unicode_ci default NULL,
  `adminpin` varchar(50) collate utf8_unicode_ci default NULL,
  `description` varchar(50) collate utf8_unicode_ci default NULL,
  `language` varchar(4) collate utf8_unicode_ci NOT NULL default 'it'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `meetme`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `natconf`
-- 

DROP TABLE IF EXISTS `natconf`;
CREATE TABLE `natconf` (
  `id` int(11) NOT NULL auto_increment,
  `port` varchar(255) collate utf8_unicode_ci NOT NULL default '5060',
  `bindaddr` varchar(255) collate utf8_unicode_ci NOT NULL default '0.0.0.0',
  `disallow` varchar(255) collate utf8_unicode_ci NOT NULL default 'all',
  `allow` varchar(255) collate utf8_unicode_ci NOT NULL default 'alaw&ulaw&gsm&g729&ilbc&g726&h261&h263&h263p',
  `context` varchar(255) collate utf8_unicode_ci NOT NULL default 'from-sip-external',
  `callerid` varchar(255) collate utf8_unicode_ci NOT NULL default 'Unknown',
  `language` varchar(255) collate utf8_unicode_ci NOT NULL default 'it',
  `registertimeout` varchar(255) collate utf8_unicode_ci NOT NULL default '20',
  `useragent` varchar(255) collate utf8_unicode_ci NOT NULL default 'Voiper PBX',
  `checkmwi` varchar(255) collate utf8_unicode_ci NOT NULL default '10',
  `srvlookup` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `maxexpirey` varchar(255) collate utf8_unicode_ci NOT NULL default '3600',
  `defaultexpirey` varchar(255) collate utf8_unicode_ci NOT NULL default '120',
  `allowguest` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `usereqphone` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `tos_sip` varchar(255) collate utf8_unicode_ci NOT NULL,
  `videosupport` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `rtptimeout` varchar(255) collate utf8_unicode_ci NOT NULL default '60',
  `rtpholdtimeout` varchar(255) collate utf8_unicode_ci NOT NULL default '300',
  `recordhistory` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `nat` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `relaxdtmf` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `musicclass` varchar(255) collate utf8_unicode_ci NOT NULL default 'default',
  `externip` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `localnet` varchar(3000) collate utf8_unicode_ci default NULL,
  `externrefresh` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `externhost` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `autodomain` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `registerattempts` varchar(255) collate utf8_unicode_ci NOT NULL default '10',
  `notifyringing` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `insecure` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `progressinband` varchar(255) collate utf8_unicode_ci NOT NULL default 'never',
  `pedantic` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `limitonpeer` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `notifyhold` varchar(255) collate utf8_unicode_ci NOT NULL default 'yes',
  `allowsubscribe` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `tos_audio` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tos_video` varchar(255) collate utf8_unicode_ci NOT NULL,
  `t38pt_udptl` varchar(255) collate utf8_unicode_ci NOT NULL default 'no',
  `rtpkeepalive` varchar(255) collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `natconf`
-- 

INSERT INTO `natconf` (`id`, `port`, `bindaddr`, `disallow`, `allow`, `context`, `callerid`, `language`, `registertimeout`, `useragent`, `checkmwi`, `srvlookup`, `maxexpirey`, `defaultexpirey`, `allowguest`, `usereqphone`, `tos_sip`, `videosupport`, `rtptimeout`, `rtpholdtimeout`, `recordhistory`, `nat`, `relaxdtmf`, `musicclass`, `externip`, `localnet`, `externrefresh`, `externhost`, `autodomain`, `registerattempts`, `notifyringing`, `insecure`, `progressinband`, `pedantic`, `limitonpeer`, `notifyhold`, `allowsubscribe`, `tos_audio`, `tos_video`, `t38pt_udptl`, `rtpkeepalive`) VALUES (1, '5060', '0.0.0.0', 'all', 'alaw&ulaw&gsm&g729&ilbc&g726&h261&h263&h263p', 'from-sip-external', 'Unknown', 'it', '20', 'Voiper PBX', '10', 'yes', '3600', '120', 'no', 'yes', '', 'yes', '60', '300', 'no', 'no', 'no', 'default', '', '192.168.0.0/255.255.255.0', '', '', 'no', '10', 'yes', 'no', 'never', 'no', 'yes', 'yes', 'no', '', '', 'no', '0');

-- --------------------------------------------------------

-- 
-- Table structure for table `phpagiconf`
-- 

DROP TABLE IF EXISTS `phpagiconf`;
CREATE TABLE `phpagiconf` (
  `phpagiid` int(11) NOT NULL auto_increment,
  `debug` tinyint(1) default NULL,
  `error_handler` tinyint(1) default NULL,
  `err_email` varchar(50) collate utf8_unicode_ci default NULL,
  `hostname` varchar(255) collate utf8_unicode_ci default NULL,
  `tempdir` varchar(255) collate utf8_unicode_ci default NULL,
  `festival_text2wave` varchar(255) collate utf8_unicode_ci default NULL,
  `asman_server` varchar(255) collate utf8_unicode_ci default NULL,
  `asman_port` int(11) NOT NULL default '0',
  `asman_user` varchar(50) collate utf8_unicode_ci default NULL,
  `asman_secret` varchar(255) collate utf8_unicode_ci default NULL,
  `cepstral_swift` varchar(255) collate utf8_unicode_ci default NULL,
  `cepstral_voice` varchar(50) collate utf8_unicode_ci default NULL,
  `setuid` tinyint(1) default NULL,
  `basedir` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`phpagiid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `phpagiconf`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `queues`
-- 

DROP TABLE IF EXISTS `queues`;
CREATE TABLE `queues` (
  `id` bigint(11) NOT NULL default '-1',
  `keyword` varchar(150) collate utf8_unicode_ci NOT NULL,
  `data` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `flags` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`,`keyword`,`data`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `queues`
-- 

INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'setinterfacevar', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'autopause', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'servicelevel', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'reportholdtime', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'eventmemberstatus', 'yes', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'eventwhencalled', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'cwignore', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'alertinfo', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'rtone', 't', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'music', 'madplay', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'password', '1234', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'name', 'FromPots', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'goto', 'vm,1000', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'maxwait', '10', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'prefix', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'monitor-join', 'yes', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'monitor-format', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'context', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'queue-thankyou', 'queue-thankyou', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'queue-callswaiting', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'queue-thereare', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'queue-youarenext', '', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'announce-holdtime', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'announce-frequency', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'callerannounce', 'InternoDisponibile', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'agentannounce', 'None', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'wrapuptime', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'retry', '1', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'timeout', '20', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'strategy', 'ringall', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'leavewhenempty', 'no', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'joinempty', 'yes', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'maxlen', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'account', '2000', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'autofill', 'yes', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'announce-round-seconds', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'periodic-announce-frequency', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'weight', '0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'member', 'Local/1000@from-internal/n,0', 0);
INSERT INTO `queues` (`id`, `keyword`, `data`, `flags`) VALUES (2000, 'member', 'Local/1001@from-internal/n,0', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `rtpconf`
-- 

DROP TABLE IF EXISTS `rtpconf`;
CREATE TABLE `rtpconf` (
  `id` int(11) NOT NULL auto_increment,
  `rtpportstart` varchar(255) collate utf8_unicode_ci default NULL,
  `rtpportend` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `rtpconf`
-- 

INSERT INTO `rtpconf` (`id`, `rtpportstart`, `rtpportend`) VALUES (1, '10000', '20000');

-- --------------------------------------------------------

-- 
-- Table structure for table `simultone`
-- 

DROP TABLE IF EXISTS `simultone`;
CREATE TABLE `simultone` (
  `id` int(11) NOT NULL auto_increment,
  `trunk_num` varchar(255) collate utf8_unicode_ci default NULL,
  `simul_num` varchar(255) collate utf8_unicode_ci default NULL,
  `description` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `simultone`
-- 

INSERT INTO `simultone` (`id`, `trunk_num`, `simul_num`, `description`) VALUES (1, 'OUT_1', '0', 'Telecom BRI Gruppo 1');

-- --------------------------------------------------------

-- 
-- Table structure for table `sip`
-- 

DROP TABLE IF EXISTS `sip`;
CREATE TABLE `sip` (
  `id` bigint(11) NOT NULL default '-1',
  `keyword` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `data` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `flags` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `sip`
-- 

INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'call-limit', '99', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'allowsubscribe', 'yes', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'cw', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'rob', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'subscribecontext', 'ext-local', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'allowcall', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'language', 'it', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 't38pt_udptl', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'videosupport', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'call-limit', '99', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'allowsubscribe', 'yes', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'cw', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'rob', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'subscribecontext', 'ext-local', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'allowcall', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'nocall', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'record_out', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'nocall', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'record_out', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'record_in', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'record_in', 'Never', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'allow', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'allow', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'disallow', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'pickupgroup', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'disallow', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'pickupgroup', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'callgroup', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'qualify', '500', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'callgroup', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'qualify', '500', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'port', '5060', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'nat', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'username', '1000', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'port', '5060', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'nat', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'username', '1001', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'mailbox', '1001@default', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'mailbox', '1000@default', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'host', 'dynamic', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'type', 'friend', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'type', 'friend', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'host', 'dynamic', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'dtmfmode', 'rfc2833', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'dtmfmode', 'rfc2833', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'context', 'from-internal', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'context', 'from-internal', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'canreinvite', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'secret', 'voiper', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'accountcode', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'account', '1001', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'canreinvite', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'secret', 'voiper', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'accountcode', '', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'account', '1000', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1000, 'callerid', '"Nome Cognome" <1000>', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'videosupport', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 't38pt_udptl', 'no', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'language', 'it', 0);
INSERT INTO `sip` (`id`, `keyword`, `data`, `flags`) VALUES (1001, 'callerid', '"Nome Cognome" <1001>', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `smtpconfig`
-- 

DROP TABLE IF EXISTS `smtpconfig`;
CREATE TABLE `smtpconfig` (
  `smtplogin` varchar(255) collate utf8_unicode_ci default NULL,
  `smtppassword` varchar(255) collate utf8_unicode_ci default NULL,
  `smtphost` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `masquarade` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fromemail` varchar(255) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `smtpconfig`
-- 

INSERT INTO `smtpconfig` (`smtplogin`, `smtppassword`, `smtphost`, `masquarade`, `fromemail`) VALUES ('', '', '', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `speednr`
-- 

DROP TABLE IF EXISTS `speednr`;
CREATE TABLE `speednr` (
  `id` int(11) NOT NULL auto_increment,
  `speednr` int(11) NOT NULL default '0',
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `telnr` varchar(255) collate utf8_unicode_ci default NULL,
  `permission` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `speednr`
-- 

INSERT INTO `speednr` (`id`, `speednr`, `name`, `telnr`, `permission`) VALUES (1, 100, 'Redirect Voicemail', '*98', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `vmconfig`
-- 

DROP TABLE IF EXISTS `vmconfig`;
CREATE TABLE `vmconfig` (
  `id` int(11) NOT NULL auto_increment,
  `format` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `serveremail` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `attach` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `maxmessage` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `minmessage` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `skipms` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `maxsilence` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `silencethreshold` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `maxlogins` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pbxskip` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fromstring` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sendvoicemail` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `review` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `operator` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `emailsubject` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `emailbody` text collate utf8_unicode_ci NOT NULL,
  `maxmsg` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `maxgreet` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `externnotify` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `externpass` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `vmconfig`
-- 

INSERT INTO `vmconfig` (`id`, `format`, `serveremail`, `attach`, `maxmessage`, `minmessage`, `skipms`, `maxsilence`, `silencethreshold`, `maxlogins`, `pbxskip`, `fromstring`, `sendvoicemail`, `review`, `operator`, `emailsubject`, `emailbody`, `maxmsg`, `maxgreet`, `externnotify`, `externpass`) VALUES (1, 'wav', 'Voiper PBX', 'yes', '180', '3', '3000', '5', '128', '3', 'yes', 'Voiper Voicemail System', 'yes', 'yes', 'no', '[Voiper PBX]: Hai ${VM_MSGNUM} nuovi messaggi nella casella vocale ${VM_MAILBOX}', 'Salve ${VM_NAME}.\\n\\n\\tHai ricevuto un messaggio Vocale della durata di ${VM_DUR}, messaggio: (numero ${VM_MSGNUM}) nella mailbox ${VM_MAILBOX} da ${VM_CALLERID}\\nin data ${VM_DATE}.\\nGrazie!\\n\\n\\t\\t\\t\\tVoiper PBX\\n', '9999', '60', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `zap`
-- 

DROP TABLE IF EXISTS `zap`;
CREATE TABLE `zap` (
  `id` bigint(11) NOT NULL default '-1',
  `keyword` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `data` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `flags` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `zap`
-- 

CREATE TABLE IF NOT EXISTS `conf` (
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `value` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `title` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` text collate utf8_unicode_ci NOT NULL,
  `lenght` varchar(10) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`name`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.username', '', 'Username', 'Name of the user for SFTP connection', '20');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.password', '', 'Password', 'Password of the user for SFTP connection', '20');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.hostname', '', 'Hostname', 'IP address of BI4Data server', '30');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.port', '', 'Port', 'Port of BI4Data server.', '3');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.hostname2', '', 'Hostname Backup', 'IP address of BI4Data backup server', '30');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.port2', '', 'Port Backup', 'Port of BI4Data backup server.', '3');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.devfilename', '/var/log/asterisk/cdr-csv/Master.csv', 'Device Filename', 'Full filename and path of Asterisk file - max 63 char (usually /var/log/asterisk/cdr-csv/Master.csv) no wildcard characters are supported.\r\n', '30');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.period', '60', 'Transfer Period', 'Seconds between two transfer check - minimum 5 seconds.', '3');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.oldfiles', 'yes', 'Process old Files', 'Set to yes to enable data collection also of the log files that are rotated daily (e.g. Master.csv.1-7) and kept for 7 days (after which the oldest file is overwritten by the new log file)\r\n', '');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.userfilename', 'voiper_cdr_data.txt', 'User Filename', 'Filename of transferred file - max 50 char.', '30');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.idleinterval', '08:00-20:00', 'Idle Interval', 'Idle time interval in which the file is not transferred in the 24 hour notation (e.g. 08:00-18:00) to avoid overload.', '12');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.minfilesize', '20', 'Minimum file size', 'Minimum file size in kbytes for transfer.', '4');
INSERT INTO `conf` (`name`, `value`, `title`, `description`, `lenght`) VALUES ('cdrpush.maxdelay', '240', 'Maximum delay', 'Maximum delay between two transfer.', '4');


