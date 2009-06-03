-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 24, 2008 at 07:57 PM
-- Server version: 5.0.58
-- PHP Version: 5.1.6
-- 
-- Database: `phonebook`
-- 

-- --------------------------------------------------------

USE `phonebook`;

-- 
-- Table structure for table `phonebook`
-- 

DROP TABLE IF EXISTS `phonebook`;
CREATE TABLE `phonebook` (
  `phonebook_id` int(10) NOT NULL auto_increment,
  `number` varchar(15) NOT NULL,
  `description` varchar(30) NOT NULL,
  PRIMARY KEY  (`phonebook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `phonebook`
-- 

