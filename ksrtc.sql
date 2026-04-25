-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jun 02, 2025 at 02:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ksrtc`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Naveen Kitageri', 'naveenkitageri7@gmail.com', 'great website to receive personal online ticket ', '2025-05-26 15:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'naveenkitageri7@gmail.com', '7c63079e556c3fd43481c09188da569b030e8eb6a5dd04ebb143a08253588684', '2025-05-28 10:17:48', '2025-05-28 07:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'ADMIN'),
(1, 'USER');

-- --------------------------------------------------------

--
-- Table structure for table `stops`
--

CREATE TABLE `stops` (
  `id` int(11) NOT NULL,
  `stops` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stops`
--

INSERT INTO `stops` (`id`, `stops`) VALUES
(1, 'CBT'),
(2, 'OLD BUS STAND'),
(3, 'HOSUR CROSS'),
(4, 'KIMS'),
(5, 'VIDYANAGAR'),
(6, 'UNAKAL'),
(7, 'BIRIDEVARAKOPPA'),
(8, 'APMC'),
(9, 'NAVANAGER'),
(10, 'ISKCON TEMPLE'),
(11, 'SDM'),
(12, 'VIDYAGIRI'),
(13, 'JUBILEE CIRCLE');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_requests`
--

CREATE TABLE `ticket_requests` (
  `id` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `path` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_requests`
--

INSERT INTO `ticket_requests` (`id`, `from`, `to`, `created_at`, `updated_at`, `path`, `user_id`, `status`, `gender`, `amount`, `result`) VALUES
(1, 3, 5, '2025-05-24 04:17:25', '2025-05-24 04:17:59', 'temp/1748060245_683148554c945.jpg', 1, 'completed', 'male', 10.00, 'result\\1748060245_683148554c945.jpg'),
(2, 1, 9, '2025-05-24 15:11:23', '2025-05-24 15:11:47', 'temp/1748099483_6831e19bca326.jpg', 1, 'completed', 'female', 0.00, 'result\\1748099483_6831e19bca326.jpg'),
(3, 5, 1, '2025-05-25 17:18:23', '2025-05-25 17:18:28', 'temp/1748193503_683350df3ba12.jpg', 1, 'completed', 'male', 20.00, 'result\\1748193503_683350df3ba12.jpg'),
(4, 1, 13, '2025-05-25 17:29:15', '2025-05-25 17:29:18', 'temp/1748194155_6833536be2e58.jpg', 1, 'completed', 'male', 60.00, 'result\\1748194155_6833536be2e58.jpg'),
(5, 1, 13, '2025-05-26 10:29:06', '2025-05-26 10:29:11', 'temp/1748255346_683442723a039.jpg', 1, 'completed', 'male', 60.00, 'result\\1748255346_683442723a039.jpg'),
(6, 1, 6, '2025-05-26 15:03:22', '2025-05-26 15:03:27', 'temp/1748271802_683482ba3858e.jpg', 1, 'completed', 'male', 25.00, 'result\\1748271802_683482ba3858e.jpg'),
(7, 2, 13, '2025-05-26 15:04:01', '2025-05-26 15:04:04', 'temp/1748271841_683482e18a71b.jpg', 1, 'completed', 'female', 0.00, 'result\\1748271841_683482e18a71b.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `email`, `password`, `first_name`, `last_name`, `created_at`) VALUES
(1, '07259308181', 'naveenkitageri7@gmail.com', '$2y$10$UyDNsIRw6jhkq3yPgpiA1ugI4H1rlNuoCzOedXVhw8zj40z99yJcG', 'Naveen', 'Kitageri', '2025-05-16 06:03:32'),
(2, '9742798686', 'naveenkitageri@gmail.com', '$2y$10$cYitwW669upOfl3zT.L.fuu1ezU.3sM3rtu7zjw9y51vWsPdV9juK', 'Naveen_k_7', 'kitageri', '2025-05-21 10:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(2, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indexes for table `stops`
--
ALTER TABLE `stops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_requests`
--
ALTER TABLE `ticket_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_v2_from_stop` (`from`),
  ADD KEY `fk_v2_to_stop` (`to`),
  ADD KEY `fk_v2_user_request` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `uq_users_phone_number` (`phone_number`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_users_roles_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stops`
--
ALTER TABLE `stops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ticket_requests`
--
ALTER TABLE `ticket_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ticket_requests`
--
ALTER TABLE `ticket_requests`
  ADD CONSTRAINT `fk_v2_from_stop` FOREIGN KEY (`from`) REFERENCES `stops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_v2_to_stop` FOREIGN KEY (`to`) REFERENCES `stops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_v2_user_request` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `fk_users_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_users_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
