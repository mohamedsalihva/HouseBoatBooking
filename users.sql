-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2025 at 03:01 AM
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'salih', 'salih@gmal.com', '$2y$10$zF/J8Sv0P0p1h.llQx4y9eHpxchq54oq4sJknVr9ttRyS0DoTuqb.', '2025-08-12 02:51:13'),
(2, 'salihva', 'salih777@gmal.com', '$2y$10$xMPO7cyu7ItJvpyHLpKnY.oIQAc2GH2wqsRSeON7M3xvF/x6kSSu.', '2025-08-12 02:56:08'),
(3, 'sae', 'salih8777@gmal.com', '$2y$10$KNlkgsyAxXCkJouF1nUJ6Ok83trxXXAW/YZkyuz0ajBwCmQES5y3q', '2025-08-12 02:57:42'),
(4, 'sae', 'salih87677@gmal.com', '$2y$10$Pgw1NQ9VffW0lc7khZX3K.q7Ai/GA2WSPell1L9XjOcKI2lTWEQSq', '2025-08-12 02:59:15'),
(5, 'wqqw', 'salih770007@gmal.com', '$2y$10$/6AlYi3yybHeRmvf0bH5dOISozo2PiSRsZTz4oGdLSc5XdqTvbpaO', '2025-08-12 03:00:26');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
