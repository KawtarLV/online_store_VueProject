-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 03, 2026 at 01:21 PM
-- Server version: 12.2.2-MariaDB-ubu2404
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Phones', '2026-03-19 11:35:10'),
(2, 'Laptops', '2026-03-19 11:35:10'),
(3, 'Headphones', '2026-03-19 11:35:10'),
(4, 'TVs', '2026-03-19 11:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Processing','Shipped','Completed','Cancelled') DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `payment_method`, `payment_status`, `created_at`) VALUES
(2, 1, 1660.49, 'Pending', 'Demo Checkout', 'Paid', '2026-04-03 12:42:12'),
(3, 1, 21.00, 'Pending', 'Demo Checkout', 'Paid', '2026-04-03 12:42:54'),
(4, 1, 21.00, 'Pending', 'Demo Checkout', 'Paid', '2026-04-03 12:43:17'),
(5, 1, 31.50, 'Pending', 'Demo Checkout', 'Paid', '2026-04-03 12:44:11'),
(6, 4, 3047.00, 'Pending', 'Demo Checkout', 'Paid', '2026-04-03 13:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(4, 2, 2, 1, 650.00),
(5, 2, 1, 1, 999.99),
(6, 2, 3, 1, 10.50),
(7, 3, 3, 2, 10.50),
(8, 4, 3, 2, 10.50),
(9, 5, 3, 3, 10.50),
(10, 6, 13, 1, 1499.00),
(11, 6, 12, 1, 249.00),
(12, 6, 14, 1, 1299.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `brand` varchar(100) DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specs`)),
  `rating` float DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `image_main` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `image_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `brand`, `specs`, `rating`, `category_id`, `created_at`, `image_main`, `image_2`, `image_3`) VALUES
(1, 'iPhone 14 Pro', 'High-end Apple smartphone with A16 chip', 999.99, 9, 'Apple', '{\"ram\": \"6GB\", \"storage\": \"128GB\", \"battery\": \"3200mAh\"}', 0, 1, '2026-03-19 11:35:22', 'images/iphone1.jpg', 'images/iphone2.jpg', NULL),
(2, 'Samsung Smart TV 55\"', '4K UHD Smart TV with HDR', 650.00, 4, 'Samsung', '{\"resolution\": \"4K\", \"size\": \"55 inch\"}', 0, 4, '2026-03-19 11:35:22', 'images/tv1.jpg', NULL, NULL),
(3, 'Test Prod 111', 'Demo', 10.50, 8, 'Demo', '{\"ram\":\"8GB\",\"storage\":\"256GB\"}', 4.5, 1, '2026-03-30 22:44:09', '/uploads/pc.jpg', NULL, NULL),
(4, 'tvv', 'fgvfvdfv', 23231.00, 222, '', NULL, 0, 4, '2026-04-03 12:56:37', '/uploads/img_69cfb905bac8f9.88129510.svg', '/uploads/img_69cfb905bf7d56.37568395.jpg', NULL),
(5, 'iPhone 15', 'Apple smartphone with A16 performance and bright OLED display', 1099.00, 14, 'Apple', '{\"storage\":\"128GB\",\"ram\":\"6GB\",\"camera\":\"48MP\"}', 4.8, 1, '2026-04-03 13:00:27', 'https://picsum.photos/seed/iphone15main/800/600', 'https://picsum.photos/seed/iphone15alt1/800/600', 'https://picsum.photos/seed/iphone15alt2/800/600'),
(6, 'Galaxy S24', 'Samsung flagship phone with strong battery life and vivid screen', 999.00, 18, 'Samsung', '{\"storage\":\"256GB\",\"ram\":\"8GB\",\"battery\":\"4000mAh\"}', 4.7, 1, '2026-04-03 13:00:27', 'https://picsum.photos/seed/galaxys24main/800/600', 'https://picsum.photos/seed/galaxys24alt1/800/600', 'https://picsum.photos/seed/galaxys24alt2/800/600'),
(7, 'Pixel 8', 'Google phone with clean Android experience and smart camera tools', 799.00, 11, 'Google', '{\"storage\":\"128GB\",\"ram\":\"8GB\",\"camera\":\"50MP\"}', 4.6, 1, '2026-04-03 13:00:27', 'https://picsum.photos/seed/pixel8main/800/600', 'https://picsum.photos/seed/pixel8alt1/800/600', 'https://picsum.photos/seed/pixel8alt2/800/600'),
(8, 'MacBook Air M3', 'Lightweight laptop for work, study, and daily productivity', 1299.00, 9, 'Apple', '{\"screen\":\"13.6 inch\",\"memory\":\"8GB\",\"storage\":\"256GB SSD\"}', 4.9, 2, '2026-04-03 13:00:27', 'https://picsum.photos/seed/macbookairm3main/800/600', 'https://picsum.photos/seed/macbookairm3alt1/800/600', 'https://picsum.photos/seed/macbookairm3alt2/800/600'),
(9, 'Dell XPS 13', 'Premium Windows laptop with compact body and sharp display', 1199.00, 7, 'Dell', '{\"screen\":\"13.4 inch\",\"memory\":\"16GB\",\"storage\":\"512GB SSD\"}', 4.7, 2, '2026-04-03 13:00:27', 'https://picsum.photos/seed/dellxps13main/800/600', 'https://picsum.photos/seed/dellxps13alt1/800/600', 'https://picsum.photos/seed/dellxps13alt2/800/600'),
(10, 'Lenovo IdeaPad 5', 'Balanced laptop for everyday tasks and office work', 749.00, 13, 'Lenovo', '{\"screen\":\"15.6 inch\",\"memory\":\"16GB\",\"storage\":\"512GB SSD\"}', 4.4, 2, '2026-04-03 13:00:27', 'https://picsum.photos/seed/ideapad5main/800/600', 'https://picsum.photos/seed/ideapad5alt1/800/600', 'https://picsum.photos/seed/ideapad5alt2/800/600'),
(11, 'Sony WH-1000XM5', 'Wireless noise cancelling headphones with premium sound', 399.00, 20, 'Sony', '{\"type\":\"Over-ear\",\"battery\":\"30 hours\",\"connectivity\":\"Bluetooth\"}', 4.9, 3, '2026-04-03 13:00:27', 'https://picsum.photos/seed/sonyxm5main/800/600', 'https://picsum.photos/seed/sonyxm5alt1/800/600', 'https://picsum.photos/seed/sonyxm5alt2/800/600'),
(12, 'AirPods Pro 2', 'Compact earbuds with active noise cancellation and spatial audio', 249.00, 23, 'Apple', '{\"type\":\"In-ear\",\"battery\":\"6 hours\",\"case\":\"MagSafe\"}', 4.8, 3, '2026-04-03 13:00:27', 'https://picsum.photos/seed/airpodspro2main/800/600', 'https://picsum.photos/seed/airpodspro2alt1/800/600', 'https://picsum.photos/seed/airpodspro2alt2/800/600'),
(13, 'LG OLED C3 55', 'OLED television with deep blacks and strong gaming support', 1499.00, 5, 'LG', '{\"size\":\"55 inch\",\"resolution\":\"4K\",\"panel\":\"OLED\"}', 4.9, 4, '2026-04-03 13:00:27', 'https://picsum.photos/seed/lgoledc3main/800/600', 'https://picsum.photos/seed/lgoledc3alt1/800/600', 'https://picsum.photos/seed/lgoledc3alt2/800/600'),
(14, 'Samsung QLED Q70 65', 'Large smart TV with bright colors and smooth motion', 1299.00, 7, 'Samsung', '{\"size\":\"65 inch\",\"resolution\":\"4K\",\"panel\":\"QLED\"}', 4.6, 4, '2026-04-03 13:00:27', 'https://picsum.photos/seed/samsungq70main/800/600', 'https://picsum.photos/seed/samsungq70alt1/800/600', 'https://picsum.photos/seed/samsungq70alt2/800/600');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `store_name`, `contact_email`, `updated_at`) VALUES
(1, 'Shop', 'shop@gmail.com', '2026-04-03 12:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$12$FMWTIJuOKo34bkLxW7x5kudM8r9xyNTsePT1EKUzotiMnn7GsXdmi', 'admin', '2026-03-19 16:52:35'),
(3, 'Test', 'test@example.com', '$2y$12$4r3xJw91BB/sQVcKUKclHuXQtfwFwQA6o9Dfnuf9f76TNOj2GhFp6', 'customer', '2026-03-30 21:33:28'),
(4, 'Kawtar', 'kawtar@gmail.com', '$2y$12$ZMNjZJZkAZUIFYvyB9nUtOnFDpKr7vXTwrTBrcZgsvkuR6bNoB.HG', 'customer', '2026-03-30 21:37:05'),
(5, 'Admin2', 'admin2@example.com', '$2y$12$OuwitsqnXnlPvFpce6JwlezD16EFSN9xOVYGcv8PsIrmWENHr5D6u', 'admin', '2026-03-30 23:10:00'),
(6, 'user', 'user@gmail.com', '$2y$12$nM4rnbEGTKFokBWPpCw3reWSo7GzGmUDWMmQKPVGOmWcDTbwluUkq', 'customer', '2026-03-30 23:11:18'),
(7, 'test2', 'test2@gmail.com', '$2y$12$nOYGmg9jyE9wYXtOdDZU.eFlw.YAcIUOWlUgNkHUQu5YCotUBoati', 'customer', '2026-03-30 23:23:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
