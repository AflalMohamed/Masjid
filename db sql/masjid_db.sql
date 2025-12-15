-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 10:33 AM
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
-- Database: `masjid_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$uJUavNTNxzyOcYDrtZ18lOU5HWeogW0OZkroDgP4N.S9tjrP7VvyG');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` enum('alert','event','general') DEFAULT 'general',
  `date` date NOT NULL DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `type`, `date`, `created_at`) VALUES
(1, 'Members Meetup', 'caisnlaks', 'alert', '2025-10-25', '2025-10-10 06:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `donor_name` varchar(200) DEFAULT NULL,
  `donor_phone` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('cash','online','other') DEFAULT 'cash',
  `note` varchar(255) DEFAULT NULL,
  `donated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `donor_name`, `donor_phone`, `amount`, `method`, `note`, `donated_at`) VALUES
(1, NULL, 'inam', '0778987654', 50000.00, 'cash', 'sjhd', '2025-10-09 13:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `start_datetime`, `end_datetime`, `location`, `created_at`) VALUES
(1, 'majilish', 'ajkhad', '2025-10-11 22:25:00', '2025-10-11 23:26:00', 'aqsa masjid', '2025-10-09 13:52:29');

-- --------------------------------------------------------

--
-- Table structure for table `facility_booking`
--

CREATE TABLE `facility_booking` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `hall_name` varchar(50) NOT NULL,
  `event_type` enum('nikah','janazah','other') NOT NULL,
  `event_date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_booking`
--

INSERT INTO `facility_booking` (`id`, `member_name`, `email`, `phone`, `hall_name`, `event_type`, `event_date`, `status`, `created_at`) VALUES
(1, 'Saja', 'sja@gmail.com', '0786347430', 'Hall 01', 'nikah', '2025-10-18', 'approved', '2025-10-10 06:19:02'),
(2, 'saha', '', '', 'Hall 01', '', '2025-10-25', 'approved', '2025-10-10 08:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `title`, `description`, `icon_url`, `created_at`) VALUES
(1, 'Prayer Schedule', 'View daily prayer times, Jummah, and Eid schedules updated automatically.', 'https://cdn-icons-png.flaticon.com/512/3076/3076438.png', '2025-10-09 13:24:02'),
(3, 'Donation & Zakat', 'Track, record, and view donation and Zakat contributions transparently.', 'https://cdn-icons-png.flaticon.com/512/2933/2933112.png', '2025-10-09 13:25:27'),
(4, 'Event Management', 'Organize community events, Ramadan programs, and special lectures easily.', 'https://cdn-icons-png.flaticon.com/512/1687/1687444.png', '2025-10-09 13:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` date NOT NULL DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finance`
--

INSERT INTO `finance` (`id`, `type`, `category`, `amount`, `description`, `date`, `created_at`) VALUES
(1, 'income', 'water', 10000.00, 'ajsbckja', '2025-10-10', '2025-10-10 06:31:03'),
(2, 'expense', 'water', 5000.00, '', '2025-10-10', '2025-10-10 06:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `user_id`, `address`, `photo`) VALUES
(1, 2, '', '1760017998_3723.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `prayer_times`
--

CREATE TABLE `prayer_times` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `fajr` time DEFAULT NULL,
  `dhuhr` time DEFAULT NULL,
  `asr` time DEFAULT NULL,
  `maghrib` time DEFAULT NULL,
  `isha` time DEFAULT NULL,
  `jummah` time DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prayer_times`
--

INSERT INTO `prayer_times` (`id`, `date`, `fajr`, `dhuhr`, `asr`, `maghrib`, `isha`, `jummah`, `note`, `created_at`) VALUES
(2, '2025-10-10', '23:09:00', '23:09:00', '01:13:00', '02:13:00', '05:13:00', '10:13:00', NULL, '2025-10-09 14:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `site_about`
--

CREATE TABLE `site_about` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_about`
--

INSERT INTO `site_about` (`id`, `title`, `description`) VALUES
(1, 'About Us', 'The Al-Aqsa Grand Jummah Masjid Management System is a digital platform designed to simplify mosque operations — from prayer schedule updates to donation management, event coordination, and community announcements. It bridges technology with spirituality, making mosque administration more efficient and transparent.');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `key`, `value`) VALUES
(1, 'masjid_name', 'Al-Aqsa Grand Jummah Masjid'),
(2, 'masjid_email', 'info@masjid.com'),
(3, 'masjid_phone', '+94 71 123 4566'),
(4, 'masjid_address', 'Batticaloa, Sri Lanka'),
(5, 'whatsapp_number', '+94711234566');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `trans_date` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$uJUavNTNxzyOcYDrtZ18lOU5HWeogW0OZkroDgP4N.S9tjrP7VvyG', 'Super Admin', 'admin@alaqsa.lk', NULL, 'admin', '2025-10-09 12:05:32'),
(2, 'sj@gmail.com', '$2y$10$LsBMUSQgOwB7MyYRrxVBAewukAi9JltLtgFX3pnqmOEKgyjTGnUca', 'sajith', 'sj@gmail.com', '0778877898', 'member', '2025-10-09 13:53:18'),
(3, 'saha', '$2y$10$AZbx3kNUQVKdu5XgGDqsVOH/7FfMCsYRwxZs4BNwGhzkqJyUrMwxa', 'saha', 'saha@gamil.com', NULL, 'member', '2025-10-09 13:59:49'),
(4, 'aflal', '$2y$10$t862F5TsYvhxJnGrnWz/lunmcH4kJj062kVyH/IJxXnBSUXJM0sfm', 'Aflal', 'afl@gmail.com', NULL, 'member', '2025-10-10 02:52:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_booking`
--
ALTER TABLE `facility_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prayer_times`
--
ALTER TABLE `prayer_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_about`
--
ALTER TABLE `site_about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `facility_booking`
--
ALTER TABLE `facility_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `finance`
--
ALTER TABLE `finance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prayer_times`
--
ALTER TABLE `prayer_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_about`
--
ALTER TABLE `site_about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
