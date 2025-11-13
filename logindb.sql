-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3399
-- Generation Time: Nov 13, 2025 at 04:20 PM
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
-- Database: `logindb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `lName` varchar(100) NOT NULL,
  `fName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `lName`, `fName`, `email`, `password`) VALUES
(1, 'ken', 'ken', 'neth@gmail.com', 'f632fa6f8c3d5f551c5df867588381ab');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `ID` int(11) NOT NULL,
  `lName` varchar(100) NOT NULL,
  `fName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `sched` varchar(100) NOT NULL,
  `counter` enum('1','2','3','4') NOT NULL,
  `slot` int(100) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `star` enum('1','2','3','4','5') NOT NULL,
  `feedback` varchar(500) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`ID`, `lName`, `fName`, `email`, `password`, `sched`, `counter`, `slot`, `is_read`, `star`, `feedback`, `status`, `updated_at`) VALUES
(17, '1', '1', '1@1.com', 'c4ca4238a0b923820dcc509a6f75849b', '2025-11-04', '1', 3, 0, '1', '', 'Approved', '2025-11-04 15:15:54'),
(18, '2', '2', '2@2.com', 'c81e728d9d4c2f636f067f89cc14862c', '2025-11-13', '1', 1, 0, '1', '', 'Pending', NULL),
(20, 'dongan', 'ryan', '3@3.com', 'eccbc87e4b5ce2fe28308fd9f2a7baf3', '2025-11-28', '3', 2, 0, '3', 'rfdfdsfd', 'Approved', NULL),
(21, 'Jasmine', 'Mico Love', '8@8.com', 'c9f0f895fb98ab9159f51fd0297e236d', '', '1', 0, 0, '1', '', 'Approved', '2025-11-04 16:45:28'),
(22, 'Dayrit', 'Giro', '9@9.com', '45c48cce2e2d7fbdea1afc51c7c6ad26', '2025-11-26', '2', 5, 0, '4', 'edasdsd', 'Pending', NULL),
(23, '10', '10', '10@10.com', 'd3d9446802a44259755d38e6d163e820', '2025-11-12', '2', 1, 0, '1', '', 'Pending', NULL),
(24, '11', '11', '11@11.com', '6512bd43d9caa6e02c990b0a82652dca', '2025-11-12', '1', 1, 0, '1', '', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registrar`
--

CREATE TABLE `registrar` (
  `id` int(11) NOT NULL,
  `lName` varchar(100) NOT NULL,
  `fName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrar`
--

INSERT INTO `registrar` (`id`, `lName`, `fName`, `email`, `password`) VALUES
(1, 'baktong', 'kurt', '5@5.com', 'e4da3b7fbbce2345d7772b0674a318d5'),
(2, 'Salita', 'Mico', '6@6.com', '1679091c5a880faf6fb5e6087eb1b2dc'),
(3, 'Mico', 'Salita', '7@7.com', '8f14e45fceea167a5a36dedd4bea2543'),
(5, 'ken', 'ken', 'ken@gmail.com', 'f632fa6f8c3d5f551c5df867588381ab');

-- --------------------------------------------------------

--
-- Table structure for table `registrar2`
--

CREATE TABLE `registrar2` (
  `id` int(11) NOT NULL,
  `fName` varchar(100) NOT NULL,
  `lName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `registrar`
--
ALTER TABLE `registrar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrar2`
--
ALTER TABLE `registrar2`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `registrar`
--
ALTER TABLE `registrar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrar2`
--
ALTER TABLE `registrar2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
