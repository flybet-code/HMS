-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2025 at 08:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

USE hms_db;

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('admin','superadmin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password_hash`, `email`, `full_name`, `phone_number`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$abcdefghijklmnopqrstuv', 'admin@example.com', 'Hospital Admin', '+251900000000', 'superadmin', '2025-02-14 00:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `laboratory_results`
--

CREATE TABLE `laboratory_results` (
  `result_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `test_type` varchar(100) NOT NULL,
  `lab_results` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laboratory_results`
--

INSERT INTO `laboratory_results` (`result_id`, `test_id`, `patient_id`, `patient_name`, `doctor_name`, `test_type`, `lab_results`) VALUES
(10, 12, 4, '', 'doc', 'bllod , urine', 'normal'),
(11, 14, 5, '', 'doc1', 'xray', 'attack'),
(12, 19, 6, '', 'doc', 'blood , urine , merak', 'normal and weba');

-- --------------------------------------------------------

--
-- Table structure for table `laboratory_test`
--

CREATE TABLE `laboratory_test` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `test_type` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Pending','Unpaid') NOT NULL DEFAULT 'Pending',
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laboratory_test`
--

INSERT INTO `laboratory_test` (`id`, `patient_id`, `user_id`, `patient_name`, `doctor_name`, `test_type`, `status`, `created_at`, `payment_status`, `price`) VALUES
(12, 4, NULL, 'Betsegaw Tadesse', 'doc', 'bllod , urine', 'Completed', '2025-02-21 23:02:19', 'Pending', NULL),
(13, 4, NULL, 'Betsegaw Tadesse', 'doc', 'hiv', 'Approved', '2025-02-21 23:20:37', 'Pending', NULL),
(14, 5, NULL, 'yehun befiqadu', 'doc1', 'xray', 'Completed', '2025-02-22 00:09:34', 'Pending', NULL),
(15, 4, NULL, 'Betsegaw Tadesse', 'doc', 'gonococos', 'Approved', '2025-02-22 00:30:14', 'Pending', 555.00),
(16, 4, NULL, 'Betsegaw Tadesse', 'doc', 'gonococos', 'Approved', '2025-02-22 01:01:01', 'Pending', 142.00),
(17, 4, NULL, 'Betsegaw Tadesse', 'doc', 'gonococos', 'Approved', '2025-02-22 01:09:16', 'Pending', 159.00),
(18, 4, NULL, 'Betsegaw Tadesse', 'doc', 'hyloga', 'Pending', '2025-02-22 22:07:12', 'Pending', 200.00),
(19, 6, NULL, 'henok civil', 'doc', 'blood , urine , merak', 'Completed', '2025-02-22 22:19:49', 'Pending', 500.00),
(20, 4, NULL, 'Betsegaw Tadesse', 'doc', 'xxxxx', 'Pending', '2025-02-23 10:47:24', 'Pending', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `medication`
--

CREATE TABLE `medication` (
  `medication_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `medications` text NOT NULL,
  `prescribed_date` datetime DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Pending','Unpaid') NOT NULL DEFAULT 'Pending',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medication`
--

INSERT INTO `medication` (`medication_id`, `patient_id`, `patient_name`, `doctor_name`, `medications`, `prescribed_date`, `payment_status`, `status`, `price`) VALUES
(1, 1, 'John Doe', 'betsegaw doctor', 'acid', '2025-02-15 04:53:54', 'Pending', 'pending', 162.00),
(5, 1, 'John Doe', 'betsegaw doctor', 'ere tew', '2025-02-15 05:27:10', 'Pending', 'pending', 162.00),
(42, 4, 'Betsegaw Tadesse', 'doc', 'paractamol , diclo', '2025-02-21 15:04:43', 'Pending', 'Approved', 123.00),
(43, 6, 'henok civil', 'doc', 'quatum , diclo', '2025-02-22 14:21:30', 'Pending', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_records`
--

CREATE TABLE `patient_records` (
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `home_number` varchar(255) DEFAULT NULL,
  `patient_type` varchar(50) DEFAULT NULL,
  `doctor_assigned` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_records`
--

INSERT INTO `patient_records` (`patient_id`, `patient_name`, `age`, `contact`, `address`, `home_number`, `patient_type`, `doctor_assigned`, `status`) VALUES
(1, 'John betsegaw', 32, '912345678', 'Addis Ababa', '12345', 'Individual', 'betsegaw doctor', 'Sent to Pharmacy'),
(2, 'Jane Doe', 28, '0923456789', 'Hawassa', '54321', 'Worker', 'betsegaw doctor', 'Sent to Pharmacy'),
(3, 'wrtyujkl', 34, '245678', 'ef', '4567', 'Individual', 'betsegaw doctor', 'Sent to Pharmacy'),
(4, 'Betsegaw Tadesse', 24, '0909078337', 'metehara', '235', 'Individual', 'betsegaw doctor', 'Assigned'),
(5, 'yehun befiqadu', 24, '0910203040', 'sheka', '008', 'Worker', 'yehun doctor', 'Assigned'),
(6, 'henok civil', 24, '0912131415', 'Hawassa', '202', 'Worker', 'betsegaw doctor', 'Assigned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','cardroom','pharmacy','cashier','laboratory') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `full_name`, `created_at`) VALUES
(1, 'doc', 'f9f16d97c90d8c6f2cab37bb6d1f1992', 'doctor', 'betsegaw doctor', '2025-02-14 00:01:41'),
(2, 'lab', '799ad83c247e4b997891c10762d12728', 'laboratory', 'yihun labo', '2025-02-14 00:01:41'),
(3, 'cash', '6ac2470ed8ccf204fd5ff89b32a355cf', 'cashier', 'josi cashier', '2025-02-14 00:01:41'),
(4, 'pharma', 'bf4e28785ab0560951dd0766f8059c4a', 'pharmacy', 'beka parma', '2025-02-14 00:01:41'),
(5, 'card', '630d7bac884a7454d2a2dbab50af914b', 'cardroom', 'gezu card', '2025-02-14 00:01:41'),
(6, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Admin', '2025-02-14 00:01:41'),
(7, 'doc1', 'f9f16d97c90d8c6f2cab37bb6d1f1992', 'doctor', 'yehun doctor', '2025-02-14 13:34:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `laboratory_results`
--
ALTER TABLE `laboratory_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `laboratory_test`
--
ALTER TABLE `laboratory_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_patient` (`patient_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `medication`
--
ALTER TABLE `medication`
  ADD PRIMARY KEY (`medication_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laboratory_results`
--
ALTER TABLE `laboratory_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `laboratory_test`
--
ALTER TABLE `laboratory_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `medication`
--
ALTER TABLE `medication`
  MODIFY `medication_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `patient_records`
--
ALTER TABLE `patient_records`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laboratory_results`
--
ALTER TABLE `laboratory_results`
  ADD CONSTRAINT `laboratory_results_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `laboratory_test` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laboratory_results_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient_records` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laboratory_test`
--
ALTER TABLE `laboratory_test`
  ADD CONSTRAINT `fk_patient` FOREIGN KEY (`patient_id`) REFERENCES `patient_records` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `medication`
--
ALTER TABLE `medication`
  ADD CONSTRAINT `medication_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient_records` (`patient_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
