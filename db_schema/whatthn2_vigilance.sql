-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 03, 2016 at 09:19 PM
-- Server version: 5.5.42-37.1-log
-- PHP Version: 5.4.31
use whatthn2_vigilance;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `whatthn2_vigilance`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `credential_id` int(11) NOT NULL,
  `text` longtext NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `post_id`, `credential_id`, `text`) VALUES
(14, 1, 1, 'intersting.'),
(13, 1, 5, 'seems cool'),
(12, 1, 5, 'haha'),
(15, 7, 11, 'No'),
(16, 7, 11, 'Not right'),
(17, 7, 11, 'Commentb3'),
(18, 7, 11, 'Comment 4'),
(19, 3, 12, 'Hello');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `credential_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_time` varchar(25) NOT NULL,
  `end_time` varchar(25) NOT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `school_id`, `credential_id`, `name`, `start_time`, `end_time`) VALUES
(1, 1, 5, 'Chemistry', '2016-03-08', '2016-03-10'),
(2, 1, 5, 'Phsysics', '2016-03-08', '2016-03-10'),
(7, 3, 5, 'asdas', '01:00', '01:00'),
(8, 8, 13, '', '19:56', '20:56');

-- --------------------------------------------------------

--
-- Table structure for table `credential`
--

CREATE TABLE IF NOT EXISTS `credential` (
  `credential_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `age` int(3) NOT NULL,
  `email` varchar(25) NOT NULL,
  `img_src` longtext NOT NULL,
  `user_type` varchar(25) NOT NULL,
  `username` varchar(25) NOT NULL,
  PRIMARY KEY (`credential_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `credential`
--

INSERT INTO `credential` (`credential_id`, `name`, `password`, `age`, `email`, `img_src`, `user_type`, `username`) VALUES
(1, 'name', 'password', 25, 'kalay@peelay.com', '043008thmy.jpg', '0', 'kalay'),
(5, 'joel branch', 'admin', 22, 'admin@email.com', '043008thmy.jpg', '0', 'admin'),
(7, 'mister', 'mister', 19, 'mister@bnb.com', '070103st', '0', 'mister'),
(8, 'Joel', 'asd1234', 0, '', '012003th', '0', 'Jbeeezy '),
(9, 'J', 'asd1234', 22, '', '022003thimage.png', '0', 'Jbeezy'),
(10, 'Joel', 'asd1234', 22, 'branchjoel@yahoo.com', '072103st', '0', 'Jbeezy'),
(11, 'Joel ', 'asd1234', 0, '', '072103stimage.png', '0', 'Jbeezy'),
(12, '', '', 0, '', '122103st', '', ''),
(13, 'Joel', 'asd1234', 0, 'branchjoel@yahoo.com', '060104st', '0', 'JB');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `organization_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `display_name` longtext NOT NULL,
  `city` varchar(25) NOT NULL,
  `email` varchar(25) NOT NULL,
  `img_src` longtext NOT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`organization_id`, `name`, `display_name`, `city`, `email`, `img_src`) VALUES
(1, 'Bernie.org', 'Bernie Sanders OrganizationV', 'Vermont', 'bernie@sanders.com', 'logov.jpg'),
(2, 'v', 'Microsoft', 'Microsoft', 'Microsoft@Microsoft.com', 'logov.jpg'),
(3, 'Meow', 'Axact', 'Tel Aviv', 'mewo@rawr.com', 'logov.jpg'),
(4, 'some', 'someCompany', 'sydney', 'syndey@aus.com', 'logov.jpg'),
(9, '', 'TrumpTower', 'Usa', 'trump@ivanka.com', 'logov.jpg'),
(10, '', 'Always More Never Less', 'Huntsville , Al', 'alwaysmore@alwaysmoreneve', 'logov.jpg'),
(11, '', '', '', '', 'logov.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `text` longtext NOT NULL,
  `media` longtext NOT NULL,
  `credential_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `text`, `media`, `credential_id`) VALUES
(1, 'This is a long text.', '072308rdmy.jpg', 1),
(5, 'cool bro.\r\n', '', 1),
(3, 'Testing.', '', 5),
(4, 'finally, its working!', '', 1),
(6, 'Hello, ', '', 9),
(7, 'Hello', '', 10),
(8, 'Test', '', 11),
(9, 'who are you people gonna vote for?', '', 1),
(10, 'Haha, good question. How about trump? :P', '', 1),
(11, 'Trump is horrible, go bernie', '', 11),
(12, 'Bernie sanders', '', 5),
(13, 'Hello buddy', '', 1),
(14, 'So like is my post getting through?', '', 5),
(15, 'Test', '', 5),
(16, 'Brandon', '', 5),
(17, '', '', 0),
(18, '', '', 5);

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE IF NOT EXISTS `school` (
  `school_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `display_name` longtext NOT NULL,
  `email` varchar(25) NOT NULL,
  `city` varchar(25) NOT NULL,
  `img_src` longtext NOT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`school_id`, `name`, `display_name`, `email`, `city`, `img_src`) VALUES
(1, 'school', 'Long School of Island', 'long@island.com', 'Long Island', 'logov.jpg'),
(4, 'MITx', 'madras@madras.com', '', '', 'logov.jpg'),
(3, 'Rhodess', 'Rhodes Island School', 'Rhodess@island.com', 'Rhodes Island', 'logov.jpg'),
(5, 'Harvard ', 'Harvard@Harvard.com', '', '', 'logov.jpg'),
(6, 'Harvard1', 'Harvard 2', 'Harvard@Harvard.com', 'Harvard 2', 'logov.jpg'),
(7, 'Microsoft', 'Microsoft', 'Microsoft@Microsoft.com', 'LA', 'logov.jpg'),
(8, '', 'Shaker heights high school', 'shaker@shaker.edu', 'Shaker heights, Ohio', 'logov.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `topfive`
--

CREATE TABLE IF NOT EXISTS `topfive` (
  `topfive_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `img_src` longtext NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  PRIMARY KEY (`topfive_id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `topfive`
--

INSERT INTO `topfive` (`topfive_id`, `user_id`, `type`, `id`, `name`, `email`, `city`, `img_src`, `unique_id`) VALUES
(4, 1, 'school', 3, 'Rhodes Island School', 'Rhodess@island.com', 'Rhodes Island', 'logov.jpg', 'school31'),
(8, 1, 'school', 6, 'Harvard 2', 'Harvard@Harvard.com', 'Harvard 2', 'logov.jpg', 'school61'),
(9, 5, 'school', 1, 'Long School of Island', 'long@island.com', 'Long Island', 'logov.jpg', 'school15'),
(10, 13, 'school', 8, 'Shaker heights high school', 'shaker@shaker.edu', 'Shaker heights, Ohio', 'logov.jpg', 'school813'),
(11, 13, 'company', 10, 'Always More Never Less', 'alwaysmore@alwaysmoreneve', 'Huntsville , Al', 'logov.jpg', 'company1013');

-- --------------------------------------------------------

--
-- Table structure for table `user_company_bridge`
--

CREATE TABLE IF NOT EXISTS `user_company_bridge` (
  `user_company_bridge_id` int(11) NOT NULL AUTO_INCREMENT,
  `credential_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  PRIMARY KEY (`user_company_bridge_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_company_bridge`
--

INSERT INTO `user_company_bridge` (`user_company_bridge_id`, `credential_id`, `organization_id`) VALUES
(1, 1, 1),
(3, 5, 1),
(4, 13, 10);

-- --------------------------------------------------------

--
-- Table structure for table `user_school_bridge`
--

CREATE TABLE IF NOT EXISTS `user_school_bridge` (
  `user_school_bridge` int(11) NOT NULL AUTO_INCREMENT,
  `credential_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  PRIMARY KEY (`user_school_bridge`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_school_bridge`
--

INSERT INTO `user_school_bridge` (`user_school_bridge`, `credential_id`, `school_id`) VALUES
(2, 1, 3),
(3, 5, 1),
(4, 13, 8);

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE IF NOT EXISTS `user_type` (
  `user_type` int(11) NOT NULL,
  `type_name` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type`, `type_name`) VALUES
(0, 'Student'),
(2, 'Organiztion'),
(4, 'Employee'),
(3, 'School');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
