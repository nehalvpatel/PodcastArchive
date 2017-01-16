-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 15, 2017 at 05:04 PM
-- Server version: 10.1.18-MariaDB-1~trusty
-- PHP Version: 5.5.9-1ubuntu4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `needl_pka`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `ID` int(11) NOT NULL,
  `Type` int(11) NOT NULL DEFAULT '1',
  `Username` text NOT NULL,
  `Name` text NOT NULL,
  `Praise` text NOT NULL,
  `Reddit` text NOT NULL,
  `Link` text NOT NULL,
  `Password` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `episodes`
--

CREATE TABLE `episodes` (
  `Identifier` varchar(255) NOT NULL,
  `Number` text NOT NULL,
  `Date` text NOT NULL,
  `Description` text NOT NULL,
  `Hosts` text NOT NULL,
  `Guests` text NOT NULL,
  `Sponsors` text NOT NULL,
  `Length` int(11) NOT NULL,
  `YouTube Length` int(11) NOT NULL,
  `Bytes` int(11) NOT NULL,
  `YouTube` text NOT NULL,
  `Twitch` text NOT NULL,
  `Published` text,
  `Reddit` text NOT NULL,
  `TimelineAuthor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `issue` text NOT NULL,
  `explanation` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `action` text NOT NULL,
  `reference_id` text NOT NULL,
  `previous_value` text,
  `new_value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `ID` int(11) NOT NULL,
  `Gender` int(11) NOT NULL DEFAULT '1',
  `Name` text NOT NULL,
  `FirstName` text NOT NULL,
  `LastName` text NOT NULL,
  `Facebook` text NOT NULL,
  `Twitter` text NOT NULL,
  `Twitch` text NOT NULL,
  `GooglePlus` text NOT NULL,
  `YouTube` text NOT NULL,
  `Reddit` text NOT NULL,
  `URL` text NOT NULL,
  `Overview` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `similar searches`
--

CREATE TABLE `similar searches` (
  `id` int(11) NOT NULL,
  `similar searches` text NOT NULL COMMENT 'Each search surrounded by quotes on either side and separated by a space.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `timestamps`
--

CREATE TABLE `timestamps` (
  `ID` int(11) NOT NULL,
  `Episode` varchar(9) NOT NULL,
  `Special` tinyint(1) NOT NULL DEFAULT '0',
  `Timestamp` int(11) NOT NULL,
  `Value` text NOT NULL,
  `URL` text NOT NULL,
  `Deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`Identifier`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `similar searches`
--
ALTER TABLE `similar searches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timestamps`
--
ALTER TABLE `timestamps`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `timestamped_episode` (`Episode`);
ALTER TABLE `timestamps` ADD FULLTEXT KEY `Value` (`Value`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=568;
--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;
--
-- AUTO_INCREMENT for table `similar searches`
--
ALTER TABLE `similar searches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `timestamps`
--
ALTER TABLE `timestamps`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14850;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
