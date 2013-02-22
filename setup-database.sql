-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 22, 2013 at 11:44 AM
-- Server version: 5.5.29
-- PHP Version: 5.4.6-1ubuntu1.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `proxy_lists`
--
CREATE DATABASE `proxy_lists` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `proxy_lists`;

-- --------------------------------------------------------

--
-- Table structure for table `access_lists`
--

CREATE TABLE IF NOT EXISTS `access_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `access_lists`
--

INSERT INTO `access_lists` (`id`, `list_name`, `file_name`) VALUES
(1, 'Allowed Sites', 'allowedsites.txt'),
(2, 'Blocked Sites', 'blockedsites.txt'),
(3, 'Excepted Sites', 'exceptedsites.txt'),
(4, 'Priority Sites', 'prioritysites.txt');

-- --------------------------------------------------------

--
-- Table structure for table `proxy_domains`
--

CREATE TABLE IF NOT EXISTS `proxy_domains` (
  `domain` varchar(255) NOT NULL,
  `access_list` int(11) NOT NULL,
  UNIQUE KEY `domain` (`domain`),
  KEY `access_list` (`access_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `proxy_domains`
--

INSERT INTO `proxy_domains` (`domain`, `access_list`) VALUES
('.firefox.com', 1),
('.mozilla.com', 1),
('.mozilla.net', 1),
('.racq.com.au', 1),
('.whereis.com', 1),
('.whitepages.com.au', 1),
('.xmarks.com', 1),
('.collingwoodfc.com.au', 2),
('.facebook.com', 2),
('.youtube.com', 2),
('.adobe.com', 4),
('.amp.com.au', 4),
('.anz.com', 4),
('.anz.com.au', 4),
('.aussie.com.au', 4),
('.avast.com', 4),
('.bankmecu.com.au', 4),
('.banksa.com.au', 4),
('.bankwest.com.au', 4),
('.bendigobank.com.au', 4),
('.boq.com.au', 4),
('.commbank.com.au', 4),
('.defencebank.com.au', 4),
('.google-analytics.com', 4),
('.google.com', 4),
('.google.com.au', 4),
('.googleapis.com', 4),
('.googleusercontent.com', 4),
('.gstatic.com', 4),
('.heritage.com.au', 4),
('.hsbc.com.au', 4),
('.ingdirect.com.au', 4),
('.macquarie.com.au', 4),
('.mebank.com.au', 4),
('.microsoft.com', 4),
('.nab.com.au', 4),
('.newcastlepermanent.com.au', 4),
('.oracle.com', 4),
('.stgeorge.com.au', 4),
('.sun.com', 4),
('.suncorp.com.au', 4),
('.suncorpbank.com.au', 4),
('.tmbank.com.au', 4),
('.verisign.com', 4),
('.westpac.com.au', 4),
('.windowsupdate.com', 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proxy_domains`
--
ALTER TABLE `proxy_domains`
  ADD CONSTRAINT `proxy_domains_ibfk_1` FOREIGN KEY (`access_list`) REFERENCES `access_lists` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
