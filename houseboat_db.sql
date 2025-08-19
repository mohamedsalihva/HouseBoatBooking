-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 19, 2025 at 02:30 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `houseboat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `boats`
--

DROP TABLE IF EXISTS `boats`;
CREATE TABLE IF NOT EXISTS `boats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `boat_name` varchar(100) NOT NULL,
  `boat_type` varchar(100) NOT NULL,
  `capacity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('available','unavailable') DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `boats`
--

INSERT INTO `boats` (`id`, `boat_name`, `boat_type`, `capacity`, `price`, `image`, `description`, `status`) VALUES
(9, 'castle', 'luxury', 30, 40000.00, '1755220854_b12.jpeg', NULL, 'available'),
(6, 'bella', 'luxury', 20, 40000.00, '1755220214_b1.jpg', NULL, 'available'),
(7, 'titanic', 'premium', 30, 20000.00, '1755220311_b6.jpg', NULL, 'available'),
(8, 'kavarat', 'standard', 10, 10000.00, '1755220347_b3.png', NULL, 'available'),
(5, 'cruse\r\n', 'standard', 67676, 7676767.00, '1755183972_3.avif', NULL, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'salih', 'salih@gmal.com', '$2y$10$zF/J8Sv0P0p1h.llQx4y9eHpxchq54oq4sJknVr9ttRyS0DoTuqb.', '2025-08-12 02:51:13', 'user'),
(2, 'salihva', 'salih777@gmal.com', '$2y$10$xMPO7cyu7ItJvpyHLpKnY.oIQAc2GH2wqsRSeON7M3xvF/x6kSSu.', '2025-08-12 02:56:08', 'user'),
(3, 'sae', 'salih8777@gmal.com', '$2y$10$KNlkgsyAxXCkJouF1nUJ6Ok83trxXXAW/YZkyuz0ajBwCmQES5y3q', '2025-08-12 02:57:42', 'user'),
(4, 'sae', 'salih87677@gmal.com', '$2y$10$Pgw1NQ9VffW0lc7khZX3K.q7Ai/GA2WSPell1L9XjOcKI2lTWEQSq', '2025-08-12 02:59:15', 'user'),
(5, 'wqqw', 'salih770007@gmal.com', '$2y$10$/6AlYi3yybHeRmvf0bH5dOISozo2PiSRsZTz4oGdLSc5XdqTvbpaO', '2025-08-12 03:00:26', 'user'),
(6, 'admin', 'admin@gmail.com', '$2y$10$QtRC8GaQi3eoosQg4IjJ0uSgImH8LfrYDzhbwQ9v9V1mFgMUWePpK', '2025-08-13 01:00:42', 'admin'),
(7, 'sali', 'sali@gmail.com', '$2y$10$aXULRssBI2fG6C4hovkJD.Y4AcIehX/n4tX1olTWCDD10hMazoj4K', '2025-08-13 01:37:31', 'user'),
(8, 'sam', 'sam@gmail.com', '$2y$10$8gu75TUqRpV8s/HZ6tgtyupk0wQV4ROvKL8hoUlg6jPcFJf8m5pKK', '2025-08-13 01:41:21', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
