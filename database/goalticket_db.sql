-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 06, 2026 at 08:41 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goalticket_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$/gHk6AH7PnXYHO4B.0FhAeHbaYMfamheZWzxopr02FewbfF5cQoPO', 'Club Administrator', 'admin', '2026-05-19 19:10:00'),
(2, 'staff1', '$2y$10$HrxBlvbq3MMWTP33dYEMQukipiQdEzijkCq8VcSWZo.N7YdAW/Z2q', 'Test Staff', 'staff', '2026-05-29 12:10:44');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `fixture_id` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(7,2) NOT NULL,
  `payment_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'completed',
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `fixture_id`, `quantity`, `total_price`, `payment_status`, `booking_date`) VALUES
(1, 1, 1, 2, 24.00, 'completed', '2026-05-19 21:29:07'),
(2, 1, 2, 1, 15.00, 'completed', '2026-05-19 21:32:32'),
(3, 1, 2, 1, 15.00, 'completed', '2026-05-20 12:25:31'),
(4, 1, 1, 1, 12.00, 'completed', '2026-05-20 13:46:24'),
(5, 1, 2, 1, 15.00, 'completed', '2026-05-20 14:41:28'),
(6, 1, 4, 3, 43.50, 'completed', '2026-05-20 14:44:52'),
(7, 1, 1, 1, 12.00, 'completed', '2026-05-20 16:07:40'),
(8, 1, 2, 3, 45.00, 'completed', '2026-05-20 17:09:57'),
(10, 1, 2, 1, 15.00, 'completed', '2026-06-04 08:03:55'),
(11, 1, 2, 1, 15.00, 'completed', '2026-06-04 10:53:54'),
(12, 1, 2, 10, 150.00, 'completed', '2026-06-10 13:38:56'),
(13, 1, 2, 1, 15.00, 'completed', '2026-06-18 11:07:38'),
(14, 1, 2, 10, 150.00, 'completed', '2026-07-02 18:32:39'),
(15, 1, 2, 1, 15.00, 'completed', '2026-07-02 18:55:13'),
(16, 1, 2, 1, 15.00, 'completed', '2026-07-25 07:34:08'),
(17, 1, 2, 1, 15.00, 'completed', '2026-08-05 14:15:56'),
(18, 1, 2, 1, 15.00, 'completed', '2026-08-05 17:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `fixtures`
--

CREATE TABLE `fixtures` (
  `fixture_id` int NOT NULL,
  `opposition` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `competition` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_date` date NOT NULL,
  `kick_off_time` time NOT NULL,
  `venue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_price` decimal(5,2) NOT NULL,
  `total_tickets` int NOT NULL,
  `tickets_sold` int DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fixtures`
--

INSERT INTO `fixtures` (`fixture_id`, `opposition`, `competition`, `match_date`, `kick_off_time`, `venue`, `ticket_price`, `total_tickets`, `tickets_sold`, `status`, `created_at`) VALUES
(1, 'Blyth Spartans', 'County Cup', '2026-08-13', '17:00:00', 'Sam Smith\'s Park', 12.00, 4, 4, 'upcoming', '2026-05-19 20:53:41'),
(2, 'Marske United', 'FA Cup', '2026-08-06', '19:00:00', 'Sam Smith\'s Park', 15.00, 500, 38, 'upcoming', '2026-05-19 21:02:18'),
(4, 'Barcelona', 'League', '2026-08-09', '16:00:00', 'Home Ground', 14.50, 10, 3, 'upcoming', '2026-05-20 14:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `qr_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'valid',
  `scanned_at` timestamp NULL DEFAULT NULL,
  `scanned_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `booking_id`, `qr_token`, `status`, `scanned_at`, `scanned_by`) VALUES
(1, 1, 'c2bab2efff4fb18f3dc3d6cf23ebd73712343d7b6488ae932ddd86830bf8a00f', 'used', '2026-05-20 14:59:47', 1),
(2, 1, 'c9ab5e8cefc5875173007cb0bdd39f06c7a665f116e0bd0782d83eaf599bc758', 'used', '2026-06-04 10:51:04', 1),
(3, 2, 'c380775d3fdb2c68652ccae42b0fc1bd6bfdc8cd4e0194ae48eb3284e5fd2423', 'used', '2026-07-02 18:36:31', 1),
(4, 3, 'd55206e717b4157d08bb90d04cee2888b4f15554266faa5caffb5b4f99485e5b', 'valid', NULL, NULL),
(5, 4, '1481e5fc13d4bf4d6df551c214166fc18f79d9d767625344f6531198dc8e4068', 'used', '2026-05-20 15:38:08', 1),
(6, 5, '7f2ad6d2eced3974d5512944d462cd1f9941f279b0bd4a58c180bb74b4c96677', 'valid', NULL, NULL),
(7, 6, '92febe613cc08edda9ef6994288f9b30009f42ff83b2e2d76d5f5cc4295bc35f', 'valid', NULL, NULL),
(8, 6, '8d3798600b0ea68e2ddb96d8b884f417ce41ee62eff29daa654b7da3c662cccd', 'valid', NULL, NULL),
(9, 6, '97c4ec084b8e34fe346d6dd911cd35740be285b33723f16fed10b19cfa1785f0', 'valid', NULL, NULL),
(10, 7, '6ac3757b3e2f7d45b72880da3adc91c1aa8ad3c000c44890d6b0e6f7e7aa4547', 'valid', NULL, NULL),
(11, 8, 'f739bba8ce5277754047fed4fd0f003bf7adc107dc9d1d94a3c0b7c9b20331b8', 'valid', NULL, NULL),
(12, 8, 'dc67872c3ae7b3342281570d36c62ba4c496bba3a46b08bac4450d6e50e48ef2', 'valid', NULL, NULL),
(13, 8, 'fb9bf94091b92196b8989da6d7cae1fa5f01a37d73c9d59752434a8bdca90b02', 'valid', NULL, NULL),
(19, 10, '1b234a7c3b7a6beb5431f02c8c7755646661a158e905095b4e20d59fd72cda22', 'valid', NULL, NULL),
(20, 11, 'a9d650ad7afb401ccb0e851b96f38ce9a95d676c9ca7ec22871595e93e1d3e28', 'valid', NULL, NULL),
(21, 12, '4142fb62be1f3900ecfff212d42b86ebbb19e685b7b4f6a54dcf207d4f7687e7', 'used', '2026-06-10 13:41:43', 1),
(22, 12, '41e18197ce9abba581fc2b45a6be6f6382f2f96f7b19a5458cad78eec798b3dc', 'valid', NULL, NULL),
(23, 12, 'aaf3fd63eb453686c41535b5637aa2b0e1485a2f350348490e6621517bb267c1', 'valid', NULL, NULL),
(24, 12, '64794638bf51214613b9d108c3f7e599cb97890c7258629a08f33d6076826f20', 'valid', NULL, NULL),
(25, 12, '0b5d42cc799d84fc42bccd322ebe9c7d78c48fc081e99e13bb1b2210e5dae0cb', 'valid', NULL, NULL),
(26, 12, '3607c4c7a878c1fb5bf57a9b4a294a6934e9f3b4a2524b03c3da0abca8e6a568', 'valid', NULL, NULL),
(27, 12, '5e6d7e978bc51cd72608d5122844eef30f95723a5a529ba47203bb0713b3d1e6', 'valid', NULL, NULL),
(28, 12, '057971731db012a0ccbd5ba45b872a5d988a4eb463b564fdfceac347bb5597a5', 'valid', NULL, NULL),
(29, 12, '912cde799d84c52b7fe5f72cd19628b0662c5970ea430983c545e31087f08c22', 'valid', NULL, NULL),
(30, 12, '1a20f194ed2d5d3b02c8fabe0635964e4f8d2d8079b080170e00117ed6dc606c', 'valid', NULL, NULL),
(31, 13, 'f9cceefe9cc84ccee1684ff58738b3bab6e40ae30e5409b00db58814b0e4a6d2', 'valid', NULL, NULL),
(32, 14, '30a90428a73cd5135f6bdcd15487fb5ad12a74070438ae0932058027179d218e', 'valid', NULL, NULL),
(33, 14, 'f504efc31769b685887a59f34649f4727220ac98cb537ad2c724342f3dc43dc3', 'valid', NULL, NULL),
(34, 14, '2964f3ac4401410654faf57299c1ddaf04279699897493d2a9e06ec0d2b0d167', 'valid', NULL, NULL),
(35, 14, '342aed96756bed1f8bf2ceee6a04acd49b0b06f1463dd7f0b871004c5950d793', 'valid', NULL, NULL),
(36, 14, 'cd7945f648bc71e3ddee08acfaf6a9204b386d5479b167a3aae92ba3ff708793', 'valid', NULL, NULL),
(37, 14, '71745782c821bd166484cfcd3ad68068afe3e7db5c1b8c404aa3f7cae8527476', 'valid', NULL, NULL),
(38, 14, '985f52a8cf46d4f561b5db1daca8de75cb8db1b60ec72ccfc29d361113e88418', 'valid', NULL, NULL),
(39, 14, '221d55e32936e18a0dc03b1394760870c7516fea5422dd9d94aec429ac76c801', 'valid', NULL, NULL),
(40, 14, '7810e12b148ac93cdab7738a83534bdee59a1177085a564b0ac0d1b861b23857', 'valid', NULL, NULL),
(41, 14, '99deaee582f3ebf422b5c4624b7f46f9f532c11ae95bc46352080e151fb27a23', 'valid', NULL, NULL),
(42, 15, '41c48b3f3af81f5374e45294e851dfb69d60f7103952eeb0de5f489c0c58afc0', 'valid', NULL, NULL),
(43, 16, 'e4abfd6bf7049e7e278ee6608120bc2b54e8a75c459832987c5e3c4a378a8228', 'valid', NULL, NULL),
(44, 17, '6ba10307dddfb31bad295b8931dcca04a3d2f26128cfd8e60d0634d2552f0ac0', 'valid', NULL, NULL),
(45, 18, 'dcb1bb3865389e5d6656e3228f46adb3a83cde2674b22815287196c72715990d', 'valid', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `created_at`) VALUES
(1, 'Ben', 'Test', 'ben@test.com', '$2y$10$j/.HVUjvI/nq.T2V0WZ7Yuiwv3ppmnr.Ub9e5mtjq2aAzVBG4xcOq', '', '2026-05-18 22:36:48'),
(2, 'Another', 'Test', 'another@test.com', '$2y$10$lecP8LNRKHlGTMFa9HbhAexyNd0O9AqZ7j4sppqh59R8jBSlFRTp6', '', '2026-05-19 21:39:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fixture_id` (`fixture_id`);

--
-- Indexes for table `fixtures`
--
ALTER TABLE `fixtures`
  ADD PRIMARY KEY (`fixture_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `qr_token` (`qr_token`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `scanned_by` (`scanned_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `fixtures`
--
ALTER TABLE `fixtures`
  MODIFY `fixture_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`fixture_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`scanned_by`) REFERENCES `admins` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
