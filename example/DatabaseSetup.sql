-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2024 at 06:21 PM
-- Server version: 10.3.39-MariaDB
-- PHP Version: 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `[Name of your database]`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT 'description',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `parent_id` int(10) UNSIGNED DEFAULT 0,
  `date` date DEFAULT current_timestamp(),
  `duedate` date DEFAULT NULL,
  `longdescription` varchar(4000) NULL DEFAULT 'longdescription',
  `start` time NULL DEFAULT '19:00:00',
  `end` time NULL DEFAULT '21:00:00',
  `location` varchar(200) NULL DEFAULT 'location'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `costitem`
--

CREATE TABLE `costitem` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(45) DEFAULT 'TBD',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `price` decimal(5,2) DEFAULT NULL,
  `type` varchar(45) DEFAULT 'W',
  `activity_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(190) DEFAULT 'description',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `parent_id` int(10) UNSIGNED DEFAULT 0,
  `name` varchar(45) DEFAULT 'name',
  `lastname` varchar(45) DEFAULT 'lastname',
  `email` varchar(45) DEFAULT 'name@domain.com',
  `role` varchar(5) NOT NULL DEFAULT 'U' COMMENT 'USER (U) or ADMIN (A)',
  `password` varchar(256) DEFAULT 'password',
  `ownpwd` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 0,
  `subscriptionuntil` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT 'TBD',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `member_id` int(10) UNSIGNED DEFAULT 0,
  `date` date DEFAULT current_timestamp(),
  `amount` decimal(6,2) DEFAULT 0.00,
  `status` varchar(12) DEFAULT NULL,
  `type` varchar(12) DEFAULT 'prepaid',
  `source` varchar(26) DEFAULT 'mollie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT 'TBD',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `member_id` int(10) UNSIGNED DEFAULT 0,
  `costitem_id` int(10) UNSIGNED DEFAULT 0,
  `payment_id` int(10) UNSIGNED DEFAULT 0,
  `quantity` int(10) UNSIGNED DEFAULT 0,
  `remark` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `costitem`
--
ALTER TABLE `costitem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `costitem`
--
ALTER TABLE `costitem`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
