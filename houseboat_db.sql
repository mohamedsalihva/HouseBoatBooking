-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 27, 2025 at 03:11 AM
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
  `status` enum('available','maintenance','booked') DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `boats`
--

INSERT INTO `boats` (`id`, `boat_name`, `boat_type`, `capacity`, `price`, `image`, `description`, `status`) VALUES
(18, 'Vembanad Queen', 'standard', 10, 60000.00, '[\"1761533088_5042_b6.jpg\",\"1761533088_4208_r6.jpg\"]', 'Cruise through the majestic Vembanad Lake in this deluxe houseboat, featuring panoramic glass windows and fine Kerala dining.', 'available'),
(19, 'Alappuzha Dreams', 'economy', 15, 10000.00, '[\"1761533180_9185_b1.png\",\"1761533180_6686_r1.jpg\"]', 'Family-friendly with local cuisine.', 'available'),
(20, 'Kettuvallam', 'luxury', 6, 12000.00, '[\"1761533290_3386_b2.jpg\",\"1761533290_7865_r2.jpeg\"]', 'Classic Kerala-style boat offering a pure backwater experience.', 'available'),
(12, 'Neelathoni', 'economy', 5, 10000.00, '[\"1761509498_1277_b7.png\",\"1761509498_2018_r7.jpg\"]', 'A luxury blue-themed boat designed for comfort and style. Enjoy peaceful rides through the calm backwaters with deluxe rooms and a rooftop deck.', 'available'),
(14, 'Pathiramanal Cruise', 'deluxe', 4, 13000.00, '[\"1761509628_5885_b10.jpg\",\"1761509628_6581_r10.jpg\"]', 'Named after the famous Pathiramanal Island, this boat offers a scenic ride filled with lush greenery, migratory birds, and calm waters.', 'available'),
(17, 'Kuttanadan Pearl', 'deluxe', 3, 20000.00, '[\"1761509763_1037_b3.jpg\",\"1761509763_5317_r3.jpg\"]', 'A premium houseboat representing the charm of Kuttanad. Spacious interiors, Kerala meals, and night stays under the stars.', 'available'),
(22, 'Royal Backwaters', 'standard', 8, 16000.00, '[\"1761533424_2862_b4.jpg\",\"1761533424_6069_r4.jpeg\"]', 'Royal Backwaters is designed for luxury travelers who want a royal stay on water. With spacious rooms, a private balcony, and a dedicated chef, it offers an unforgettable cruise through the calm waters of Kumarakom.', 'available'),
(23, 'Emerald Queen', 'economy', 10, 23000.00, '[\"1761533480_6343_b9.jpeg\",\"1761533480_8245_r9.jpg\"]', 'Emerald Queen offers a blend of heritage and modern comfort. The boat has elegant wooden interiors, an open lounge area, and provides traditional Kerala meals prepared onboard.', 'available'),
(26, 'Lotus Crown', 'deluxe', 7, 23000.00, '[\"1761533908_7525_b12.jpeg\",\"1761533908_8164_r12.jpg\"]', 'Lotus Crown is a deluxe houseboat offering an excellent cruise experience across the scenic Vembanad Lake. It includes private cabins, an upper deck for panoramic views, and freshly prepared seafood dishes.', 'available'),
(27, 'Coconut Paradise', 'economy', 20, 9000.00, '[\"1761533967_6882_b13.avif\"]', 'Coconut Paradise gives guests a traditional Kerala experience surrounded by coconut groves and calm waters. Enjoy relaxing on the deck while watching local village life and sunset views.', 'available'),
(28, 'Vallam Thoni', 'standard', 18, 3000.00, '[\"1761534055_5114_b14.jpg\",\"1761534055_8185_r14.jpg\"]', 'A classic wooden houseboat designed like the ancient vallam, giving a truly traditional Kerala backwater experience', 'available'),
(33, 'Periyar cruise', 'luxury', 9, 14000.00, '[\"1761534612_1393_b16.jpg\",\"1761534612_7361_r15.jpg\"]', 'Ideal for family trips, offering calm backwater views and local village sightseeing.', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `boat_id` int NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `guests` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `boat_id` (`boat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `boat_id`, `checkin_date`, `checkout_date`, `guests`, `total_price`, `status`, `payment_method`, `payment_status`, `transaction_id`, `created_at`) VALUES (1, '9', 17, '2025-10-29', 2025-10-30, 2, '20000.00', 'confirmed', net_banking, 'completed', TXN17615329858911, '2025-10-27 02:43:05')

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(8, 'sam', 'sam@gmail.com', '$2y$10$8gu75TUqRpV8s/HZ6tgtyupk0wQV4ROvKL8hoUlg6jPcFJf8m5pKK', '2025-08-13 01:41:21', 'user'),
(9, 'san', 'san@gmail.com', '$2y$10$VKsiGp0X4GPaby0Rz1ggv.TnIDgWpCf5oCBUI3AmZukrs5OvIrcgO', '2025-10-26 20:01:29', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
