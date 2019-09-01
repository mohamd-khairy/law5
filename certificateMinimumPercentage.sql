-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2019 at 05:12 PM
-- Server version: 5.7.27-0ubuntu0.18.04.1
-- PHP Version: 7.2.19-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `law5_seed_php_be`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificateMinimumPercentage`
--

CREATE TABLE `certificateMinimumPercentage` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `certificateTypeId` int(11) NOT NULL,
  `fromDate` date DEFAULT NULL,
  `minimumPercentage` double NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificateMinimumPercentage`
--

INSERT INTO `certificateMinimumPercentage` (`id`, `certificateTypeId`, `fromDate`, `minimumPercentage`, `isDeleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2019-06-13', 52.5, 0, '2019-08-29 07:26:10', '2019-08-29 09:33:32', NULL),
(14, 1, '2019-06-14', 100, 0, '2019-08-29 09:09:12', '2019-08-29 09:09:12', NULL),
(15, 2, '2019-06-14', 52.5, 0, '2019-08-29 09:29:03', '2019-08-29 09:29:03', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificateMinimumPercentage`
--
ALTER TABLE `certificateMinimumPercentage`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificateMinimumPercentage`
--
ALTER TABLE `certificateMinimumPercentage`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
