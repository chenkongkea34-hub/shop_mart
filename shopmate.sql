-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 28, 2026 at 01:29 PM
-- Server version: 5.7.40
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpaid',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_orders_order_number` (`order_number`),
  KEY `FK_orders_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `order_date`, `total_amount`, `status`, `customer_name`, `customer_phone`, `shipping_address`, `payment_method`, `payment_status`, `created_at`) VALUES
(6, 4, 'SM-20261021143219-d8fdde3b240f', '2026-10-21 14:32:19.757850', '176.50', 'Pending', 'Chen Kongkea', '0923742345e', 'ee', 'cod', 'Paid', '2026-10-21 14:32:19.757850'),
(8, 4, 'SM-20261022130936-564b7e702b73', '2026-10-22 13:09:36.415319', '101.48', 'Pending', 'bath emliy', '07764567', 'sreab', 'cod', 'Unpaid', '2026-10-22 13:09:36.415319'),
(9, 12, 'SM-20261026064301-08f6d4c9c9be', '2026-10-26 06:43:01.496903', '191.47', 'Pending', 'bath', '0923742345', 'pp', 'qr', 'Paid', '2026-10-26 06:43:01.496903'),
(10, 13, 'SM-20261026125407-e664cb3369b2', '2026-10-26 12:54:07.529722', '15.00', 'Pending', 'so pheK', '0923742345', 'PP', 'cod', 'Paid', '2026-10-26 12:54:07.529722'),
(11, 12, 'SM-20261026133448-5a1779194fd8', '2026-10-26 13:34:48.365360', '13.50', 'Pending', 'Chen Kongkea', '0923785294', 'pp', 'cod', 'Paid', '2026-10-26 13:34:48.365360'),
(12, 13, 'SM-20261026133719-97e24a9fa7ad', '2026-10-26 13:37:19.131510', '15.00', 'Pending', 'sopheak', '0923742345', 'sr', 'cod', 'Paid', '2026-10-26 13:37:19.131510'),
(13, 14, 'SM-20261027094050-d4a8e0746bac', '2026-10-27 09:40:50.745121', '5.00', 'Pending', 'srey neang', '0885566188', 'pp', 'cod', 'Paid', '2026-10-27 09:40:50.745121'),
(14, 15, 'SM-20261028113100-ebedccea06eb', '2026-10-28 11:31:00.890527', '4.40', 'Pending', 'bath emliy', '0885566188', 'pp', 'qr', 'Unpaid', '2026-10-28 11:31:00.890527'),
(15, 15, 'SM-20261028113218-3e93bca4bd4f', '2026-10-28 11:32:18.715005', '4.40', 'Pending', 'bath emliy', '0885566188', 'pp', 'qr', 'Unpaid', '2026-10-28 11:32:18.715005'),
(16, 15, 'SM-20261028113429-6db6466e050b', '2026-10-28 11:34:29.200602', '4.40', 'Pending', 'bath emliy', '0923785294', 'pp', 'qr', 'Paid', '2026-10-28 11:34:29.200602'),
(17, 14, 'SM-20261028120009-a76eb9e778e7', '2026-10-28 12:00:09.927469', '11.50', 'Pending', 'srey neang', '02759420', 'sr', 'qr', 'Paid', '2026-10-28 12:00:09.927469'),
(18, 16, 'SM-20261028131543-5898c2808bbd', '2026-10-28 13:15:43.919426', '7.90', 'Pending', 'mey', '038947530235', 'sr', 'qr', 'Paid', '2026-10-28 13:15:43.919426');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`quantity` * `unit_price`)) STORED,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`),
  KEY `FK_order_items_orders` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `created_at`) VALUES
(7, 6, 15, 'Camera Degital', 1, '175.00', '2026-10-21 14:32:19.757850'),
(11, 8, 2, 'Wireless Headphones', 1, '59.99', '2026-10-22 13:09:36.415319'),
(12, 8, 3, 'Travel Backpack', 1, '39.99', '2026-10-22 13:09:36.415319'),
(13, 9, 1, 'Smart Watch Series 5', 1, '89.99', '2026-10-26 06:43:01.496903'),
(14, 9, 2, 'Wireless Headphones', 1, '59.99', '2026-10-26 06:43:01.496903'),
(15, 9, 3, 'Travel Backpack', 1, '39.99', '2026-10-26 06:43:01.496903'),
(16, 10, 2, 'banana', 1, '2.00', '2026-10-26 12:54:07.529722'),
(17, 10, 1, 'sting', 1, '1.50', '2026-10-26 12:54:07.529722'),
(18, 10, 3, 'cherry', 1, '10.00', '2026-10-26 12:54:07.529722'),
(19, 11, 3, 'cherry', 1, '10.00', '2026-10-26 13:34:48.365360'),
(20, 11, 2, 'banana', 1, '2.00', '2026-10-26 13:34:48.365360'),
(21, 12, 3, 'cherry', 1, '10.00', '2026-10-26 13:37:19.131510'),
(22, 12, 2, 'banana', 1, '2.00', '2026-10-26 13:37:19.131510'),
(23, 12, 1, 'sting', 1, '1.50', '2026-10-26 13:37:19.131510'),
(24, 13, 2, 'banana', 1, '2.00', '2026-10-27 09:40:50.745121'),
(25, 13, 1, 'sting', 1, '1.50', '2026-10-27 09:40:50.745121'),
(26, 14, 7, 'Mii', 1, '1.50', '2026-10-28 11:31:00.890527'),
(27, 14, 6, 'yogart', 1, '1.40', '2026-10-28 11:31:00.890527'),
(28, 15, 7, 'Mii', 1, '1.50', '2026-10-28 11:32:18.715005'),
(29, 15, 6, 'yogart', 1, '1.40', '2026-10-28 11:32:18.715005'),
(30, 16, 7, 'Mii', 1, '1.50', '2026-10-28 11:34:29.200602'),
(31, 16, 6, 'yogart', 1, '1.40', '2026-10-28 11:34:29.200602'),
(32, 17, 3, 'cherry', 1, '10.00', '2026-10-28 12:00:09.927469'),
(33, 18, 6, 'yogart', 1, '1.40', '2026-10-28 13:15:43.919426'),
(34, 18, 4, 'meat beef', 1, '5.00', '2026-10-28 13:15:43.919426');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'General',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `created_at`, `category`) VALUES
(1, 'sting', '1.50', '', '1b407e14a797debb73468ba7284ad141.jpg', '2026-10-26 13:46:11.302650', 'General'),
(2, 'banana', '2.00', '', '1379b6828cc663785c2f5241ed231617.jpg', '2026-10-26 19:31:21.743494', 'Fruits & Vegetables'),
(3, 'cherry', '10.00', '', '1d982148c9ff21e55b98648ff87799ef.jpg', '2026-10-26 19:36:20.465947', 'Fruits & Vegetables'),
(4, 'meat beef', '5.00', '', '0641f3ab8c20c58e6fc04f70f1720571.jpg', '2026-10-26 20:17:57.691971', 'Meat & Seafood'),
(6, 'yogart', '1.40', '', '3945f0dc5275b96870a908550d7f71fb.jpg', '2026-10-28 15:03:43.289349', 'Beverages'),
(7, 'Mii', '1.50', '', 'd0b6ea200108e66d13457b94cb230f48.jpg', '2026-10-28 18:18:13.210328', 'Snacks');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'Chen Kongkea', 'chenkongkea34@gmail.com', '$2y$10$FWO71/16H6w0iS0aPzeT6eEyXT31UPolGePvGAIoH7QCKVHARqF.q', 'customer', '2026-10-21 13:33:03'),
(11, 'admin', 'admin@gmail.com', '$2y$10$caTutGSMT6PqX8RZB3ock.giRe88TxTNDnv8T3szoSeHmuszzUsl.', 'admin', '2026-10-22 13:15:31'),
(12, 'bath', 'bath12345@gamil.com', '$2y$10$kn7b2JW5GryrcWk1wReZm.Eju./7PxIHWZVRXsBpcYo/5buX9/c52', 'customer', '2026-10-26 06:41:42'),
(13, 'sopheak12', 'sopheak12@gmail.com', '$2y$10$4zO4bwqQ.QJEzXAKa1VMWekXqNms1fheBO3a5yMcr7ORonTsMBoYG', 'customer', '2026-10-26 12:53:41'),
(14, 'srey neang', 'sreyneang1219@gmail.com', '$2y$10$7Fq295PHfWtm1y45mTdcdOPxQBv6B1GNA3nONYtHs6Jtbas2pIkRe', 'customer', '2026-10-27 09:39:44'),
(15, 'bath emliy', 'bathemliy34@gmail.com', '$2y$10$JCre.7BSwsCW7s2OUBoz0uW9E2Pp.3GR3cIxdpwaOgTdVT86W5LHO', 'customer', '2026-10-28 11:30:21'),
(16, 'mey', 'mey123@gamil.com', '$2y$10$POURTqKPIDI0NQhrHSdDmeaxUwAWhQwFUXRSw4AIo0Ji33XXSQnS.', 'customer', '2026-10-28 13:15:12');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `FK_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
