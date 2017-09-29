-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2016 at 05:07 AM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_ipath`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_devices`
--

CREATE TABLE `m_devices` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8mb4 NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_devices`
--

INSERT INTO `m_devices` (`id`, `name`, `created`, `modified`) VALUES
(1, 'vvxvdsfq121312313sss', '2016-09-29 02:42:43', '2016-09-29 02:42:43'),
(2, 'vvxvdsfq121312313sss13123', '2016-09-29 02:42:58', '2016-09-29 02:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `m_images`
--

CREATE TABLE `m_images` (
  `id` int(11) NOT NULL,
  `name` varchar(300) CHARACTER SET utf8mb4 NOT NULL,
  `route_id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `m_locations`
--

CREATE TABLE `m_locations` (
  `id` int(11) NOT NULL,
  `longitude` varchar(300) CHARACTER SET utf8mb4 NOT NULL,
  `latitude` varchar(300) CHARACTER SET utf8mb4 NOT NULL,
  `rotation` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_locations`
--

INSERT INTO `m_locations` (`id`, `longitude`, `latitude`, `rotation`, `route_id`, `image_id`, `type`, `created`, `modified`) VALUES
(1, '123', '12312', 0, 1, 1, 1, '2016-09-28 00:00:00', '2016-09-30 00:00:00'),
(2, '213', '2342342', 0, 2, 2, 2, '2016-09-29 00:00:00', '2016-09-30 00:00:00'),
(3, '213123', '123', 0, 3, 3, 3, '2016-09-15 00:00:00', '2016-09-23 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `m_routes`
--

CREATE TABLE `m_routes` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `main` varchar(300) NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `country` varchar(300) CHARACTER SET utf8mb4 NOT NULL,
  `number_image` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_routes`
--

INSERT INTO `m_routes` (`id`, `device_id`, `main`, `time_start`, `time_end`, `country`, `number_image`, `created`, `modified`) VALUES
(1, 1, '', '2016-09-21 09:09:34', '2016-09-23 09:09:34', 'Việt Nam', 2, '2016-09-29 02:42:52', '2016-09-29 02:42:52'),
(2, 2, '', '2016-09-21 09:09:34', '2016-09-23 09:09:34', 'Việt Nam', 2, '2016-09-29 02:42:59', '2016-09-29 02:42:59');

-- --------------------------------------------------------

--
-- Table structure for table `m_users`
--

CREATE TABLE `m_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_users`
--

INSERT INTO `m_users` (`id`, `username`, `password`, `created`, `modified`) VALUES
(15, 'vietvv7292@gmail.com', '25f9e794323b453885f5181f1b624d0b', '2016-09-21 09:09:34', '2016-09-21 09:09:34'),
(16, 'vietvvs7292@gmail.com', '25f9e794323b453885f5181f1b624d0b', '2016-09-21 09:09:57', '2016-09-21 09:09:57'),
(17, 'vietvv7292@gmail.coms', '25f9e794323b453885f5181f1b624d0b', '2016-09-21 09:20:37', '2016-09-21 09:20:37'),
(18, 'vietvdv7292@gmail.com', '25f9e794323b453885f5181f1b624d0b', '2016-09-21 09:51:40', '2016-09-21 09:51:40'),
(19, 'vietvv7292sdfdsfdsf@gmail.com', '25f9e794323b453885f5181f1b624d0b', NULL, NULL),
(20, 'visdfsfsdetvv7292@gmail.com', '25f9e794323b453885f5181f1b624d0b', '2016-09-21 10:06:00', '2016-09-21 10:06:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_devices`
--
ALTER TABLE `m_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_images`
--
ALTER TABLE `m_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_locations`
--
ALTER TABLE `m_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_routes`
--
ALTER TABLE `m_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_users`
--
ALTER TABLE `m_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_devices`
--
ALTER TABLE `m_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `m_images`
--
ALTER TABLE `m_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `m_locations`
--
ALTER TABLE `m_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `m_routes`
--
ALTER TABLE `m_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `m_users`
--
ALTER TABLE `m_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
