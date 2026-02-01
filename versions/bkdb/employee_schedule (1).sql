-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 03:52 AM
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
-- Database: `employee_schedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `action_description` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `affected_table` varchar(100) DEFAULT NULL,
  `affected_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `severity` enum('low','medium','high','critical') DEFAULT 'low',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action_type`, `action_description`, `ip_address`, `user_agent`, `affected_table`, `affected_id`, `old_values`, `new_values`, `severity`, `created_at`) VALUES
(29, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-28 01:42:24'),
(30, 193, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":6,\"afternoon_count\":10,\"max_concurrent_leave\":3}', '{\"morning_count\":10,\"afternoon_count\":10,\"max_concurrent_leave\":3,\"level_rules\":{\"level1\":{\"min\":10,\"max\":10},\"level2\":{\"min\":10,\"max\":10},\"level3\":{\"min\":3,\"max\":6}},\"holiday_rules\":{\"morning_count\":3,\"afternoon_count\":3,\"night_count\":3,\"min_level\":2}}', 'medium', '2025-11-28 01:44:16'),
(31, 193, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":10,\"afternoon_count\":10,\"max_concurrent_leave\":3}', '{\"morning_count\":5,\"afternoon_count\":5,\"max_concurrent_leave\":3,\"level_rules\":{\"level1\":{\"min\":10,\"max\":10},\"level2\":{\"min\":10,\"max\":10},\"level3\":{\"min\":3,\"max\":6}},\"holiday_rules\":{\"morning_count\":3,\"afternoon_count\":3,\"night_count\":3,\"min_level\":2}}', 'medium', '2025-11-28 01:53:06'),
(32, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-28 02:04:31'),
(33, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-28 04:07:51'),
(34, 193, 'schedule_generate', 'Generated 749 schedules for 2025-12', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-12\",\"count\":749}', 'medium', '2025-11-28 04:10:25'),
(35, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-28 04:24:40'),
(36, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-28 04:36:24'),
(37, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-28 04:38:40'),
(38, 193, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":5,\"afternoon_count\":5,\"max_concurrent_leave\":3}', '{\"morning_count\":5,\"afternoon_count\":5,\"max_concurrent_leave\":3,\"level_rules\":{\"level1\":{\"min\":10,\"max\":10},\"level2\":{\"min\":10,\"max\":10},\"level3\":{\"min\":3,\"max\":6}},\"holiday_rules\":{\"morning_count\":3,\"afternoon_count\":3,\"night_count\":3,\"min_level\":2}}', 'medium', '2025-11-28 05:33:00'),
(39, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-29 10:25:03'),
(40, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-29 11:02:42'),
(41, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-29 11:02:49'),
(42, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:47:42'),
(43, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:47:46'),
(44, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:52:21'),
(45, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:52:34'),
(46, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:53:24'),
(47, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 11:53:59'),
(48, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 12:07:20'),
(49, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 12:07:23'),
(50, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 12:07:25'),
(51, 193, 'session_timeout', 'Session expired due to inactivity', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL, 'low', '2025-11-29 13:32:05'),
(52, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-29 13:32:12'),
(53, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 13:32:16'),
(54, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 14:15:18'),
(55, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 14:15:21'),
(56, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-29 14:21:32'),
(57, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:06:06'),
(58, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:06:11'),
(59, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:06:14'),
(60, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:06:27'),
(61, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:06:30'),
(62, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:08:46'),
(63, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:08:54'),
(64, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:09:01'),
(65, 193, 'schedule_generate', 'Generated 749 schedules for 2025-10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-10\",\"count\":749}', 'medium', '2025-11-30 02:20:05'),
(66, 193, 'schedule_generate', 'Generated 749 schedules for 2025-12', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-12\",\"count\":749}', 'medium', '2025-11-30 02:27:08'),
(67, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:32:13'),
(68, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:32:19'),
(69, 193, 'schedule_generate', 'Generated 700 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":700}', 'medium', '2025-11-30 02:36:00'),
(70, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:36:07'),
(71, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:43:04'),
(72, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:45:21'),
(73, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:48:04'),
(74, 193, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:48:11'),
(75, 193, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 193, NULL, NULL, 'low', '2025-11-30 02:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(1, 'แผนกบุคคล', '2025-11-14 11:48:47'),
(2, 'แผนกบัญชี', '2025-11-14 11:48:47'),
(3, 'แผนกไอที', '2025-11-14 11:48:47'),
(4, 'แผนกการตลาด', '2025-11-14 11:48:47'),
(5, 'แผนกผลิต', '2025-11-14 11:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `employee_level_rules`
--

CREATE TABLE `employee_level_rules` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `employee_level` int(11) NOT NULL,
  `min_per_day` int(11) DEFAULT 0,
  `max_per_day` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_level_rules`
--

INSERT INTO `employee_level_rules` (`id`, `department_id`, `employee_level`, `min_per_day`, `max_per_day`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10, 10, 2, '2025-11-20 03:40:48', '2025-11-28 05:33:00'),
(2, 1, 2, 10, 10, 2, '2025-11-20 03:40:48', '2025-11-28 05:33:00'),
(3, 1, 3, 3, 6, 2, '2025-11-20 03:40:48', '2025-11-28 05:33:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_login_attempts`
--

CREATE TABLE `failed_login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `future_leave_requests`
--

CREATE TABLE `future_leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `request_type` enum('vacation','personal','sick') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `future_swap_requests`
--

CREATE TABLE `future_swap_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `original_date` date NOT NULL,
  `target_date` date NOT NULL,
  `original_shift_type` enum('morning','afternoon','night','day','night_shift','morning_afternoon','morning_night','afternoon_night') NOT NULL,
  `target_shift_type` enum('morning','afternoon','night','day','night_shift','morning_afternoon','morning_night','afternoon_night') NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_rules`
--

CREATE TABLE `holiday_rules` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `morning_count` int(11) DEFAULT 2,
  `afternoon_count` int(11) DEFAULT 2,
  `night_count` int(11) DEFAULT 2,
  `min_employee_level` int(11) DEFAULT 2,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holiday_rules`
--

INSERT INTO `holiday_rules` (`id`, `department_id`, `morning_count`, `afternoon_count`, `night_count`, `min_employee_level`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, 2, 2, 2, '2025-11-20 03:40:48', '2025-11-28 05:33:00');

-- --------------------------------------------------------

--
-- Table structure for table `holiday_settings`
--

CREATE TABLE `holiday_settings` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `holiday_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `request_type` enum('sick_leave','holiday','swap') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `medical_certificate` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 1 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `report_type` enum('leave','swap','attendance') NOT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `schedule_date` date NOT NULL,
  `shift_type` enum('morning','afternoon','night','day','night_shift','morning_afternoon','morning_night','afternoon_night') NOT NULL,
  `is_future_schedule` tinyint(1) DEFAULT 0,
  `planned_month` year(4) NOT NULL DEFAULT 2024,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_holiday` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `user_id`, `department_id`, `schedule_date`, `shift_type`, `is_future_schedule`, `planned_month`, `created_at`, `is_holiday`) VALUES
(16521, 227, 1, '2025-10-01', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16522, 222, 1, '2025-10-01', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16523, 235, 1, '2025-10-01', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16524, 211, 1, '2025-10-01', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16525, 228, 1, '2025-10-01', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16526, 219, 1, '2025-10-01', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16527, 230, 1, '2025-10-01', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16528, 207, 1, '2025-10-01', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16529, 233, 1, '2025-10-01', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16530, 224, 1, '2025-10-01', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16531, 217, 1, '2025-10-01', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16532, 193, 1, '2025-10-01', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16533, 237, 1, '2025-10-01', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16534, 215, 1, '2025-10-01', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16535, 194, 1, '2025-10-01', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16536, 206, 1, '2025-10-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16537, 225, 1, '2025-10-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16538, 203, 1, '2025-10-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16539, 197, 1, '2025-10-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16540, 208, 1, '2025-10-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16541, 213, 1, '2025-10-01', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16542, 223, 1, '2025-10-01', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16543, 216, 1, '2025-10-01', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16544, 196, 1, '2025-10-01', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16545, 205, 1, '2025-10-01', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16546, 204, 1, '2025-10-01', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16547, 234, 1, '2025-10-01', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16548, 205, 1, '2025-10-02', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16549, 231, 1, '2025-10-02', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16550, 200, 1, '2025-10-02', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16551, 198, 1, '2025-10-02', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16552, 237, 1, '2025-10-02', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16553, 238, 1, '2025-10-02', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16554, 210, 1, '2025-10-02', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16555, 224, 1, '2025-10-02', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16556, 217, 1, '2025-10-02', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16557, 201, 1, '2025-10-02', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16558, 199, 1, '2025-10-02', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16559, 195, 1, '2025-10-02', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16560, 211, 1, '2025-10-02', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16561, 227, 1, '2025-10-02', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16562, 233, 1, '2025-10-02', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16563, 206, 1, '2025-10-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16564, 194, 1, '2025-10-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16565, 196, 1, '2025-10-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16566, 214, 1, '2025-10-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16567, 208, 1, '2025-10-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16568, 209, 1, '2025-10-02', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16569, 228, 1, '2025-10-02', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16570, 235, 1, '2025-10-02', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16571, 221, 1, '2025-10-02', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16572, 218, 1, '2025-10-02', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16573, 223, 1, '2025-10-02', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16574, 222, 1, '2025-10-02', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16575, 222, 1, '2025-10-03', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16576, 224, 1, '2025-10-03', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16577, 235, 1, '2025-10-03', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16578, 227, 1, '2025-10-03', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16579, 232, 1, '2025-10-03', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16580, 239, 1, '2025-10-03', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16581, 210, 1, '2025-10-03', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16582, 196, 1, '2025-10-03', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16583, 236, 1, '2025-10-03', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16584, 219, 1, '2025-10-03', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16585, 201, 1, '2025-10-03', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16586, 206, 1, '2025-10-03', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16587, 200, 1, '2025-10-03', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16588, 211, 1, '2025-10-03', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16589, 205, 1, '2025-10-03', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16590, 209, 1, '2025-10-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16591, 195, 1, '2025-10-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16592, 194, 1, '2025-10-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16593, 214, 1, '2025-10-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16594, 197, 1, '2025-10-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16595, 233, 1, '2025-10-03', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16596, 213, 1, '2025-10-03', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16597, 223, 1, '2025-10-03', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16598, 216, 1, '2025-10-03', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16599, 215, 1, '2025-10-03', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16600, 218, 1, '2025-10-03', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16601, 217, 1, '2025-10-03', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16602, 239, 1, '2025-10-04', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16603, 222, 1, '2025-10-04', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16604, 237, 1, '2025-10-04', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16605, 194, 1, '2025-10-04', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16606, 198, 1, '2025-10-04', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16607, 195, 1, '2025-10-04', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16608, 233, 1, '2025-10-04', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16609, 202, 1, '2025-10-04', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16610, 230, 1, '2025-10-04', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16611, 223, 1, '2025-10-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16612, 238, 1, '2025-10-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16613, 207, 1, '2025-10-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16614, 205, 1, '2025-10-04', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16615, 193, 1, '2025-10-04', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16616, 208, 1, '2025-10-04', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16617, 219, 1, '2025-10-04', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16618, 213, 1, '2025-10-05', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16619, 195, 1, '2025-10-05', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16620, 236, 1, '2025-10-05', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16621, 238, 1, '2025-10-05', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16622, 202, 1, '2025-10-05', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16623, 224, 1, '2025-10-05', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16624, 208, 1, '2025-10-05', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16625, 235, 1, '2025-10-05', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16626, 223, 1, '2025-10-05', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16627, 212, 1, '2025-10-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16628, 234, 1, '2025-10-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16629, 226, 1, '2025-10-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16630, 219, 1, '2025-10-05', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16631, 230, 1, '2025-10-05', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16632, 222, 1, '2025-10-05', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16633, 211, 1, '2025-10-05', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16634, 222, 1, '2025-10-06', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16635, 232, 1, '2025-10-06', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16636, 235, 1, '2025-10-06', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16637, 230, 1, '2025-10-06', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16638, 237, 1, '2025-10-06', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16639, 216, 1, '2025-10-06', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16640, 226, 1, '2025-10-06', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16641, 208, 1, '2025-10-06', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16642, 193, 1, '2025-10-06', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16643, 225, 1, '2025-10-06', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16644, 204, 1, '2025-10-06', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16645, 224, 1, '2025-10-06', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16646, 203, 1, '2025-10-06', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16647, 196, 1, '2025-10-06', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16648, 233, 1, '2025-10-06', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16649, 215, 1, '2025-10-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16650, 221, 1, '2025-10-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16651, 239, 1, '2025-10-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16652, 209, 1, '2025-10-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16653, 227, 1, '2025-10-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16654, 229, 1, '2025-10-06', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16655, 206, 1, '2025-10-06', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16656, 202, 1, '2025-10-06', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16657, 195, 1, '2025-10-06', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16658, 236, 1, '2025-10-06', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16659, 211, 1, '2025-10-06', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16660, 200, 1, '2025-10-06', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16661, 214, 1, '2025-10-07', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16662, 194, 1, '2025-10-07', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16663, 197, 1, '2025-10-07', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16664, 196, 1, '2025-10-07', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16665, 221, 1, '2025-10-07', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16666, 226, 1, '2025-10-07', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16667, 229, 1, '2025-10-07', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16668, 234, 1, '2025-10-07', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16669, 238, 1, '2025-10-07', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16670, 206, 1, '2025-10-07', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16671, 213, 1, '2025-10-07', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16672, 210, 1, '2025-10-07', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16673, 209, 1, '2025-10-07', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16674, 224, 1, '2025-10-07', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16675, 200, 1, '2025-10-07', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16676, 227, 1, '2025-10-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16677, 211, 1, '2025-10-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16678, 222, 1, '2025-10-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16679, 217, 1, '2025-10-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16680, 231, 1, '2025-10-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16681, 203, 1, '2025-10-07', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16682, 232, 1, '2025-10-07', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16683, 235, 1, '2025-10-07', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16684, 225, 1, '2025-10-07', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16685, 239, 1, '2025-10-07', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16686, 219, 1, '2025-10-07', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16687, 201, 1, '2025-10-07', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16688, 199, 1, '2025-10-08', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16689, 207, 1, '2025-10-08', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16690, 220, 1, '2025-10-08', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16691, 239, 1, '2025-10-08', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16692, 236, 1, '2025-10-08', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16693, 237, 1, '2025-10-08', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16694, 222, 1, '2025-10-08', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16695, 214, 1, '2025-10-08', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16696, 215, 1, '2025-10-08', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16697, 218, 1, '2025-10-08', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16698, 206, 1, '2025-10-08', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16699, 221, 1, '2025-10-08', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16700, 211, 1, '2025-10-08', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16701, 233, 1, '2025-10-08', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16702, 195, 1, '2025-10-08', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16703, 200, 1, '2025-10-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16704, 194, 1, '2025-10-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16705, 204, 1, '2025-10-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16706, 224, 1, '2025-10-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16707, 234, 1, '2025-10-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16708, 213, 1, '2025-10-08', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16709, 193, 1, '2025-10-08', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16710, 196, 1, '2025-10-08', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16711, 230, 1, '2025-10-08', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16712, 209, 1, '2025-10-08', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16713, 235, 1, '2025-10-08', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16714, 227, 1, '2025-10-08', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16715, 205, 1, '2025-10-09', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16716, 221, 1, '2025-10-09', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16717, 238, 1, '2025-10-09', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16718, 236, 1, '2025-10-09', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16719, 200, 1, '2025-10-09', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16720, 207, 1, '2025-10-09', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16721, 194, 1, '2025-10-09', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16722, 204, 1, '2025-10-09', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16723, 219, 1, '2025-10-09', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16724, 216, 1, '2025-10-09', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16725, 227, 1, '2025-10-09', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16726, 199, 1, '2025-10-09', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16727, 233, 1, '2025-10-09', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16728, 230, 1, '2025-10-09', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16729, 212, 1, '2025-10-09', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16730, 206, 1, '2025-10-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16731, 223, 1, '2025-10-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16732, 218, 1, '2025-10-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16733, 211, 1, '2025-10-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16734, 202, 1, '2025-10-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16735, 217, 1, '2025-10-09', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16736, 208, 1, '2025-10-09', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16737, 239, 1, '2025-10-09', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16738, 198, 1, '2025-10-09', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16739, 228, 1, '2025-10-09', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16740, 229, 1, '2025-10-09', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16741, 193, 1, '2025-10-09', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16742, 233, 1, '2025-10-10', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16743, 209, 1, '2025-10-10', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16744, 221, 1, '2025-10-10', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16745, 217, 1, '2025-10-10', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16746, 229, 1, '2025-10-10', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16747, 210, 1, '2025-10-10', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16748, 227, 1, '2025-10-10', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16749, 231, 1, '2025-10-10', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16750, 194, 1, '2025-10-10', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16751, 238, 1, '2025-10-10', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16752, 232, 1, '2025-10-10', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16753, 212, 1, '2025-10-10', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16754, 202, 1, '2025-10-10', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16755, 206, 1, '2025-10-10', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16756, 208, 1, '2025-10-10', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16757, 214, 1, '2025-10-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16758, 195, 1, '2025-10-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16759, 234, 1, '2025-10-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16760, 226, 1, '2025-10-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16761, 213, 1, '2025-10-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16762, 193, 1, '2025-10-10', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16763, 219, 1, '2025-10-10', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16764, 222, 1, '2025-10-10', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16765, 223, 1, '2025-10-10', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16766, 203, 1, '2025-10-10', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16767, 211, 1, '2025-10-10', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16768, 197, 1, '2025-10-10', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16769, 238, 1, '2025-10-11', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16770, 232, 1, '2025-10-11', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16771, 204, 1, '2025-10-11', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16772, 228, 1, '2025-10-11', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16773, 226, 1, '2025-10-11', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16774, 193, 1, '2025-10-11', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16775, 202, 1, '2025-10-11', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16776, 218, 1, '2025-10-11', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16777, 217, 1, '2025-10-11', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16778, 200, 1, '2025-10-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16779, 210, 1, '2025-10-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16780, 212, 1, '2025-10-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16781, 198, 1, '2025-10-11', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16782, 227, 1, '2025-10-11', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16783, 205, 1, '2025-10-11', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16784, 239, 1, '2025-10-11', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16785, 228, 1, '2025-10-12', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16786, 199, 1, '2025-10-12', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16787, 224, 1, '2025-10-12', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16788, 221, 1, '2025-10-12', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16789, 237, 1, '2025-10-12', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16790, 195, 1, '2025-10-12', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16791, 234, 1, '2025-10-12', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16792, 214, 1, '2025-10-12', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16793, 205, 1, '2025-10-12', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16794, 218, 1, '2025-10-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16795, 198, 1, '2025-10-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16796, 223, 1, '2025-10-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16797, 209, 1, '2025-10-12', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16798, 230, 1, '2025-10-12', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16799, 229, 1, '2025-10-12', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16800, 196, 1, '2025-10-12', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16801, 213, 1, '2025-10-13', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16802, 215, 1, '2025-10-13', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16803, 199, 1, '2025-10-13', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16804, 235, 1, '2025-10-13', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16805, 231, 1, '2025-10-13', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16806, 196, 1, '2025-10-13', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16807, 226, 1, '2025-10-13', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16808, 203, 1, '2025-10-13', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16809, 230, 1, '2025-10-13', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16810, 233, 1, '2025-10-13', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16811, 239, 1, '2025-10-13', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16812, 200, 1, '2025-10-13', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16813, 204, 1, '2025-10-13', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16814, 201, 1, '2025-10-13', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16815, 228, 1, '2025-10-13', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16816, 195, 1, '2025-10-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16817, 205, 1, '2025-10-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16818, 225, 1, '2025-10-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16819, 214, 1, '2025-10-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16820, 220, 1, '2025-10-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16821, 234, 1, '2025-10-13', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16822, 222, 1, '2025-10-13', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16823, 212, 1, '2025-10-13', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16824, 221, 1, '2025-10-13', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16825, 217, 1, '2025-10-13', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16826, 218, 1, '2025-10-13', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16827, 219, 1, '2025-10-13', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16828, 234, 1, '2025-10-14', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16829, 203, 1, '2025-10-14', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16830, 194, 1, '2025-10-14', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16831, 229, 1, '2025-10-14', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16832, 236, 1, '2025-10-14', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16833, 204, 1, '2025-10-14', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16834, 227, 1, '2025-10-14', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16835, 197, 1, '2025-10-14', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16836, 223, 1, '2025-10-14', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16837, 238, 1, '2025-10-14', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16838, 231, 1, '2025-10-14', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16839, 218, 1, '2025-10-14', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16840, 195, 1, '2025-10-14', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16841, 215, 1, '2025-10-14', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16842, 221, 1, '2025-10-14', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16843, 225, 1, '2025-10-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16844, 205, 1, '2025-10-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16845, 198, 1, '2025-10-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16846, 222, 1, '2025-10-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16847, 226, 1, '2025-10-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16848, 207, 1, '2025-10-14', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16849, 208, 1, '2025-10-14', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16850, 232, 1, '2025-10-14', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16851, 219, 1, '2025-10-14', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16852, 224, 1, '2025-10-14', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16853, 193, 1, '2025-10-14', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16854, 202, 1, '2025-10-14', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16855, 230, 1, '2025-10-15', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16856, 203, 1, '2025-10-15', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16857, 215, 1, '2025-10-15', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16858, 229, 1, '2025-10-15', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16859, 211, 1, '2025-10-15', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16860, 225, 1, '2025-10-15', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16861, 218, 1, '2025-10-15', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16862, 193, 1, '2025-10-15', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16863, 196, 1, '2025-10-15', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16864, 227, 1, '2025-10-15', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16865, 228, 1, '2025-10-15', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16866, 237, 1, '2025-10-15', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16867, 220, 1, '2025-10-15', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16868, 195, 1, '2025-10-15', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16869, 198, 1, '2025-10-15', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16870, 222, 1, '2025-10-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16871, 232, 1, '2025-10-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16872, 194, 1, '2025-10-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16873, 238, 1, '2025-10-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16874, 214, 1, '2025-10-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16875, 205, 1, '2025-10-15', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16876, 235, 1, '2025-10-15', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16877, 199, 1, '2025-10-15', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16878, 206, 1, '2025-10-15', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16879, 212, 1, '2025-10-15', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16880, 208, 1, '2025-10-15', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16881, 239, 1, '2025-10-15', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16882, 219, 1, '2025-10-16', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16883, 199, 1, '2025-10-16', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16884, 237, 1, '2025-10-16', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16885, 223, 1, '2025-10-16', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16886, 228, 1, '2025-10-16', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16887, 217, 1, '2025-10-16', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16888, 231, 1, '2025-10-16', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16889, 225, 1, '2025-10-16', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16890, 205, 1, '2025-10-16', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16891, 203, 1, '2025-10-16', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16892, 215, 1, '2025-10-16', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16893, 227, 1, '2025-10-16', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16894, 200, 1, '2025-10-16', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16895, 229, 1, '2025-10-16', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16896, 220, 1, '2025-10-16', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16897, 212, 1, '2025-10-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16898, 238, 1, '2025-10-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16899, 233, 1, '2025-10-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16900, 197, 1, '2025-10-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16901, 236, 1, '2025-10-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16902, 222, 1, '2025-10-16', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16903, 213, 1, '2025-10-16', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16904, 198, 1, '2025-10-16', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16905, 211, 1, '2025-10-16', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16906, 218, 1, '2025-10-16', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16907, 204, 1, '2025-10-16', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16908, 193, 1, '2025-10-16', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16909, 195, 1, '2025-10-17', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16910, 230, 1, '2025-10-17', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16911, 233, 1, '2025-10-17', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16912, 196, 1, '2025-10-17', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16913, 217, 1, '2025-10-17', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16914, 197, 1, '2025-10-17', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16915, 201, 1, '2025-10-17', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16916, 237, 1, '2025-10-17', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16917, 225, 1, '2025-10-17', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16918, 212, 1, '2025-10-17', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16919, 194, 1, '2025-10-17', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16920, 226, 1, '2025-10-17', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16921, 216, 1, '2025-10-17', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16922, 202, 1, '2025-10-17', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16923, 213, 1, '2025-10-17', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16924, 232, 1, '2025-10-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16925, 200, 1, '2025-10-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16926, 223, 1, '2025-10-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16927, 236, 1, '2025-10-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16928, 198, 1, '2025-10-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16929, 214, 1, '2025-10-17', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16930, 203, 1, '2025-10-17', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16931, 231, 1, '2025-10-17', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16932, 209, 1, '2025-10-17', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16933, 211, 1, '2025-10-17', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16934, 218, 1, '2025-10-17', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16935, 220, 1, '2025-10-17', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16936, 195, 1, '2025-10-18', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16937, 201, 1, '2025-10-18', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16938, 194, 1, '2025-10-18', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16939, 225, 1, '2025-10-18', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16940, 238, 1, '2025-10-18', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16941, 203, 1, '2025-10-18', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16942, 232, 1, '2025-10-18', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16943, 199, 1, '2025-10-18', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16944, 198, 1, '2025-10-18', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16945, 207, 1, '2025-10-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16946, 229, 1, '2025-10-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16947, 223, 1, '2025-10-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16948, 196, 1, '2025-10-18', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16949, 193, 1, '2025-10-18', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16950, 224, 1, '2025-10-18', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16951, 230, 1, '2025-10-18', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16952, 209, 1, '2025-10-19', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16953, 198, 1, '2025-10-19', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16954, 221, 1, '2025-10-19', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(16955, 217, 1, '2025-10-19', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16956, 193, 1, '2025-10-19', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16957, 205, 1, '2025-10-19', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16958, 225, 1, '2025-10-19', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16959, 218, 1, '2025-10-19', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16960, 231, 1, '2025-10-19', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(16961, 207, 1, '2025-10-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16962, 235, 1, '2025-10-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16963, 222, 1, '2025-10-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(16964, 213, 1, '2025-10-19', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16965, 199, 1, '2025-10-19', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16966, 230, 1, '2025-10-19', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16967, 200, 1, '2025-10-19', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(16968, 210, 1, '2025-10-20', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16969, 231, 1, '2025-10-20', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16970, 237, 1, '2025-10-20', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16971, 220, 1, '2025-10-20', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16972, 195, 1, '2025-10-20', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16973, 196, 1, '2025-10-20', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16974, 235, 1, '2025-10-20', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16975, 227, 1, '2025-10-20', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16976, 232, 1, '2025-10-20', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16977, 236, 1, '2025-10-20', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16978, 224, 1, '2025-10-20', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16979, 212, 1, '2025-10-20', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16980, 226, 1, '2025-10-20', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16981, 199, 1, '2025-10-20', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16982, 213, 1, '2025-10-20', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(16983, 230, 1, '2025-10-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16984, 198, 1, '2025-10-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16985, 214, 1, '2025-10-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16986, 202, 1, '2025-10-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16987, 228, 1, '2025-10-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(16988, 234, 1, '2025-10-20', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16989, 193, 1, '2025-10-20', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16990, 201, 1, '2025-10-20', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16991, 217, 1, '2025-10-20', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16992, 239, 1, '2025-10-20', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16993, 207, 1, '2025-10-20', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16994, 206, 1, '2025-10-20', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(16995, 202, 1, '2025-10-21', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16996, 214, 1, '2025-10-21', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16997, 196, 1, '2025-10-21', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16998, 217, 1, '2025-10-21', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(16999, 201, 1, '2025-10-21', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17000, 215, 1, '2025-10-21', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17001, 207, 1, '2025-10-21', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17002, 226, 1, '2025-10-21', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17003, 210, 1, '2025-10-21', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17004, 224, 1, '2025-10-21', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17005, 208, 1, '2025-10-21', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17006, 227, 1, '2025-10-21', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17007, 216, 1, '2025-10-21', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17008, 200, 1, '2025-10-21', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17009, 219, 1, '2025-10-21', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17010, 225, 1, '2025-10-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17011, 193, 1, '2025-10-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17012, 206, 1, '2025-10-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17013, 204, 1, '2025-10-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17014, 209, 1, '2025-10-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17015, 218, 1, '2025-10-21', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17016, 238, 1, '2025-10-21', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17017, 197, 1, '2025-10-21', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17018, 239, 1, '2025-10-21', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17019, 229, 1, '2025-10-21', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17020, 228, 1, '2025-10-21', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17021, 223, 1, '2025-10-21', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17022, 216, 1, '2025-10-22', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17023, 211, 1, '2025-10-22', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17024, 204, 1, '2025-10-22', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17025, 228, 1, '2025-10-22', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17026, 234, 1, '2025-10-22', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17027, 200, 1, '2025-10-22', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17028, 201, 1, '2025-10-22', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17029, 219, 1, '2025-10-22', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17030, 239, 1, '2025-10-22', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17031, 230, 1, '2025-10-22', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17032, 238, 1, '2025-10-22', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17033, 237, 1, '2025-10-22', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17034, 207, 1, '2025-10-22', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17035, 231, 1, '2025-10-22', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17036, 198, 1, '2025-10-22', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17037, 224, 1, '2025-10-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17038, 227, 1, '2025-10-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17039, 214, 1, '2025-10-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17040, 215, 1, '2025-10-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17041, 206, 1, '2025-10-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17042, 232, 1, '2025-10-22', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17043, 197, 1, '2025-10-22', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17044, 235, 1, '2025-10-22', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17045, 220, 1, '2025-10-22', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17046, 218, 1, '2025-10-22', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17047, 199, 1, '2025-10-22', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17048, 217, 1, '2025-10-22', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17049, 225, 1, '2025-10-23', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17050, 227, 1, '2025-10-23', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17051, 207, 1, '2025-10-23', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17052, 237, 1, '2025-10-23', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17053, 206, 1, '2025-10-23', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17054, 224, 1, '2025-10-23', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17055, 213, 1, '2025-10-23', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17056, 214, 1, '2025-10-23', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17057, 211, 1, '2025-10-23', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17058, 197, 1, '2025-10-23', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17059, 236, 1, '2025-10-23', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17060, 226, 1, '2025-10-23', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17061, 200, 1, '2025-10-23', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17062, 201, 1, '2025-10-23', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17063, 229, 1, '2025-10-23', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17064, 216, 1, '2025-10-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17065, 235, 1, '2025-10-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17066, 199, 1, '2025-10-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17067, 209, 1, '2025-10-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17068, 232, 1, '2025-10-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17069, 230, 1, '2025-10-23', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17070, 234, 1, '2025-10-23', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17071, 204, 1, '2025-10-23', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17072, 238, 1, '2025-10-23', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17073, 221, 1, '2025-10-23', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17074, 223, 1, '2025-10-23', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17075, 215, 1, '2025-10-23', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17076, 239, 1, '2025-10-24', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17077, 227, 1, '2025-10-24', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17078, 201, 1, '2025-10-24', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17079, 217, 1, '2025-10-24', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17080, 238, 1, '2025-10-24', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17081, 202, 1, '2025-10-24', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17082, 207, 1, '2025-10-24', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17083, 218, 1, '2025-10-24', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17084, 197, 1, '2025-10-24', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17085, 214, 1, '2025-10-24', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17086, 209, 1, '2025-10-24', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17087, 223, 1, '2025-10-24', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17088, 228, 1, '2025-10-24', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17089, 236, 1, '2025-10-24', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17090, 224, 1, '2025-10-24', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17091, 216, 1, '2025-10-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17092, 213, 1, '2025-10-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17093, 222, 1, '2025-10-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17094, 233, 1, '2025-10-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17095, 219, 1, '2025-10-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17096, 194, 1, '2025-10-24', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17097, 196, 1, '2025-10-24', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17098, 230, 1, '2025-10-24', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17099, 229, 1, '2025-10-24', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17100, 235, 1, '2025-10-24', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17101, 195, 1, '2025-10-24', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17102, 232, 1, '2025-10-24', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17103, 213, 1, '2025-10-25', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17104, 214, 1, '2025-10-25', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17105, 229, 1, '2025-10-25', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17106, 206, 1, '2025-10-25', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17107, 232, 1, '2025-10-25', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17108, 233, 1, '2025-10-25', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17109, 202, 1, '2025-10-25', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17110, 231, 1, '2025-10-25', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17111, 199, 1, '2025-10-25', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17112, 197, 1, '2025-10-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17113, 235, 1, '2025-10-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17114, 208, 1, '2025-10-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17115, 212, 1, '2025-10-25', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17116, 196, 1, '2025-10-25', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17117, 221, 1, '2025-10-25', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17118, 224, 1, '2025-10-25', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17119, 237, 1, '2025-10-26', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17120, 200, 1, '2025-10-26', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17121, 224, 1, '2025-10-26', 'morning', 0, '2024', '2025-11-30 02:20:05', 1),
(17122, 223, 1, '2025-10-26', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17123, 204, 1, '2025-10-26', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17124, 196, 1, '2025-10-26', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17125, 232, 1, '2025-10-26', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17126, 205, 1, '2025-10-26', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17127, 217, 1, '2025-10-26', 'night', 0, '2024', '2025-11-30 02:20:05', 1),
(17128, 238, 1, '2025-10-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17129, 206, 1, '2025-10-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17130, 216, 1, '2025-10-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 1),
(17131, 211, 1, '2025-10-26', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17132, 202, 1, '2025-10-26', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17133, 208, 1, '2025-10-26', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17134, 231, 1, '2025-10-26', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 1),
(17135, 210, 1, '2025-10-27', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17136, 200, 1, '2025-10-27', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17137, 235, 1, '2025-10-27', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17138, 197, 1, '2025-10-27', 'morning', 0, '2024', '2025-11-30 02:20:05', 0);
INSERT INTO `schedules` (`id`, `user_id`, `department_id`, `schedule_date`, `shift_type`, `is_future_schedule`, `planned_month`, `created_at`, `is_holiday`) VALUES
(17139, 205, 1, '2025-10-27', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17140, 218, 1, '2025-10-27', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17141, 224, 1, '2025-10-27', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17142, 221, 1, '2025-10-27', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17143, 208, 1, '2025-10-27', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17144, 227, 1, '2025-10-27', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17145, 232, 1, '2025-10-27', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17146, 233, 1, '2025-10-27', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17147, 213, 1, '2025-10-27', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17148, 220, 1, '2025-10-27', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17149, 211, 1, '2025-10-27', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17150, 229, 1, '2025-10-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17151, 206, 1, '2025-10-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17152, 231, 1, '2025-10-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17153, 198, 1, '2025-10-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17154, 209, 1, '2025-10-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17155, 223, 1, '2025-10-27', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17156, 215, 1, '2025-10-27', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17157, 193, 1, '2025-10-27', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17158, 204, 1, '2025-10-27', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17159, 230, 1, '2025-10-27', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17160, 236, 1, '2025-10-27', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17161, 203, 1, '2025-10-27', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17162, 214, 1, '2025-10-28', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17163, 238, 1, '2025-10-28', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17164, 234, 1, '2025-10-28', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17165, 227, 1, '2025-10-28', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17166, 209, 1, '2025-10-28', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17167, 212, 1, '2025-10-28', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17168, 211, 1, '2025-10-28', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17169, 213, 1, '2025-10-28', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17170, 223, 1, '2025-10-28', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17171, 233, 1, '2025-10-28', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17172, 232, 1, '2025-10-28', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17173, 220, 1, '2025-10-28', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17174, 201, 1, '2025-10-28', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17175, 204, 1, '2025-10-28', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17176, 195, 1, '2025-10-28', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17177, 231, 1, '2025-10-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17178, 197, 1, '2025-10-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17179, 210, 1, '2025-10-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17180, 208, 1, '2025-10-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17181, 235, 1, '2025-10-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17182, 202, 1, '2025-10-28', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17183, 199, 1, '2025-10-28', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17184, 222, 1, '2025-10-28', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17185, 193, 1, '2025-10-28', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17186, 230, 1, '2025-10-28', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17187, 226, 1, '2025-10-28', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17188, 203, 1, '2025-10-28', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17189, 235, 1, '2025-10-29', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17190, 200, 1, '2025-10-29', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17191, 212, 1, '2025-10-29', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17192, 239, 1, '2025-10-29', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17193, 233, 1, '2025-10-29', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17194, 223, 1, '2025-10-29', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17195, 201, 1, '2025-10-29', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17196, 204, 1, '2025-10-29', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17197, 213, 1, '2025-10-29', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17198, 193, 1, '2025-10-29', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17199, 208, 1, '2025-10-29', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17200, 232, 1, '2025-10-29', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17201, 227, 1, '2025-10-29', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17202, 209, 1, '2025-10-29', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17203, 214, 1, '2025-10-29', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17204, 202, 1, '2025-10-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17205, 216, 1, '2025-10-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17206, 231, 1, '2025-10-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17207, 215, 1, '2025-10-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17208, 210, 1, '2025-10-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17209, 207, 1, '2025-10-29', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17210, 236, 1, '2025-10-29', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17211, 217, 1, '2025-10-29', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17212, 203, 1, '2025-10-29', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17213, 225, 1, '2025-10-29', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17214, 224, 1, '2025-10-29', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17215, 238, 1, '2025-10-29', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17216, 224, 1, '2025-10-30', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17217, 236, 1, '2025-10-30', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17218, 208, 1, '2025-10-30', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17219, 195, 1, '2025-10-30', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17220, 207, 1, '2025-10-30', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17221, 201, 1, '2025-10-30', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17222, 234, 1, '2025-10-30', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17223, 202, 1, '2025-10-30', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17224, 221, 1, '2025-10-30', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17225, 218, 1, '2025-10-30', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17226, 211, 1, '2025-10-30', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17227, 232, 1, '2025-10-30', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17228, 199, 1, '2025-10-30', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17229, 210, 1, '2025-10-30', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17230, 233, 1, '2025-10-30', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17231, 222, 1, '2025-10-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17232, 213, 1, '2025-10-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17233, 200, 1, '2025-10-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17234, 229, 1, '2025-10-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17235, 204, 1, '2025-10-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17236, 228, 1, '2025-10-30', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17237, 238, 1, '2025-10-30', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17238, 197, 1, '2025-10-30', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17239, 226, 1, '2025-10-30', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17240, 206, 1, '2025-10-30', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17241, 230, 1, '2025-10-30', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17242, 237, 1, '2025-10-30', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17243, 238, 1, '2025-10-31', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17244, 212, 1, '2025-10-31', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17245, 221, 1, '2025-10-31', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17246, 232, 1, '2025-10-31', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17247, 239, 1, '2025-10-31', 'morning', 0, '2024', '2025-11-30 02:20:05', 0),
(17248, 200, 1, '2025-10-31', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17249, 237, 1, '2025-10-31', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17250, 224, 1, '2025-10-31', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17251, 226, 1, '2025-10-31', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17252, 223, 1, '2025-10-31', 'afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17253, 214, 1, '2025-10-31', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17254, 220, 1, '2025-10-31', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17255, 233, 1, '2025-10-31', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17256, 206, 1, '2025-10-31', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17257, 197, 1, '2025-10-31', 'night', 0, '2024', '2025-11-30 02:20:05', 0),
(17258, 194, 1, '2025-10-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17259, 235, 1, '2025-10-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17260, 203, 1, '2025-10-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17261, 225, 1, '2025-10-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17262, 218, 1, '2025-10-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:20:05', 0),
(17263, 199, 1, '2025-10-31', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17264, 213, 1, '2025-10-31', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17265, 211, 1, '2025-10-31', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17266, 201, 1, '2025-10-31', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17267, 209, 1, '2025-10-31', 'morning_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17268, 227, 1, '2025-10-31', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17269, 208, 1, '2025-10-31', 'afternoon_night', 0, '2024', '2025-11-30 02:20:05', 0),
(17270, 236, 1, '2025-12-01', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17271, 232, 1, '2025-12-01', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17272, 234, 1, '2025-12-01', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17273, 206, 1, '2025-12-01', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17274, 237, 1, '2025-12-01', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17275, 193, 1, '2025-12-01', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17276, 199, 1, '2025-12-01', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17277, 211, 1, '2025-12-01', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17278, 195, 1, '2025-12-01', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17279, 235, 1, '2025-12-01', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17280, 208, 1, '2025-12-01', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17281, 228, 1, '2025-12-01', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17282, 215, 1, '2025-12-01', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17283, 210, 1, '2025-12-01', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17284, 207, 1, '2025-12-01', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17285, 238, 1, '2025-12-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17286, 201, 1, '2025-12-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17287, 222, 1, '2025-12-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17288, 239, 1, '2025-12-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17289, 219, 1, '2025-12-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17290, 200, 1, '2025-12-01', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17291, 223, 1, '2025-12-01', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17292, 198, 1, '2025-12-01', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17293, 213, 1, '2025-12-01', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17294, 216, 1, '2025-12-01', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17295, 218, 1, '2025-12-01', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17296, 231, 1, '2025-12-01', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17297, 193, 1, '2025-12-02', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17298, 211, 1, '2025-12-02', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17299, 224, 1, '2025-12-02', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17300, 207, 1, '2025-12-02', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17301, 221, 1, '2025-12-02', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17302, 196, 1, '2025-12-02', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17303, 225, 1, '2025-12-02', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17304, 232, 1, '2025-12-02', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17305, 205, 1, '2025-12-02', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17306, 223, 1, '2025-12-02', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17307, 237, 1, '2025-12-02', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17308, 200, 1, '2025-12-02', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17309, 202, 1, '2025-12-02', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17310, 222, 1, '2025-12-02', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17311, 234, 1, '2025-12-02', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17312, 220, 1, '2025-12-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17313, 218, 1, '2025-12-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17314, 210, 1, '2025-12-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17315, 206, 1, '2025-12-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17316, 209, 1, '2025-12-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17317, 239, 1, '2025-12-02', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17318, 199, 1, '2025-12-02', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17319, 203, 1, '2025-12-02', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17320, 213, 1, '2025-12-02', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17321, 230, 1, '2025-12-02', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17322, 208, 1, '2025-12-02', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17323, 219, 1, '2025-12-02', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17324, 228, 1, '2025-12-03', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17325, 201, 1, '2025-12-03', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17326, 214, 1, '2025-12-03', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17327, 220, 1, '2025-12-03', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17328, 199, 1, '2025-12-03', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17329, 237, 1, '2025-12-03', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17330, 195, 1, '2025-12-03', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17331, 200, 1, '2025-12-03', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17332, 227, 1, '2025-12-03', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17333, 210, 1, '2025-12-03', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17334, 223, 1, '2025-12-03', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17335, 226, 1, '2025-12-03', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17336, 204, 1, '2025-12-03', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17337, 193, 1, '2025-12-03', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17338, 194, 1, '2025-12-03', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17339, 198, 1, '2025-12-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17340, 236, 1, '2025-12-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17341, 229, 1, '2025-12-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17342, 235, 1, '2025-12-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17343, 208, 1, '2025-12-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17344, 222, 1, '2025-12-03', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17345, 239, 1, '2025-12-03', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17346, 209, 1, '2025-12-03', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17347, 225, 1, '2025-12-03', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17348, 215, 1, '2025-12-03', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17349, 224, 1, '2025-12-03', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17350, 203, 1, '2025-12-03', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17351, 227, 1, '2025-12-04', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17352, 204, 1, '2025-12-04', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17353, 210, 1, '2025-12-04', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17354, 199, 1, '2025-12-04', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17355, 208, 1, '2025-12-04', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17356, 232, 1, '2025-12-04', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17357, 202, 1, '2025-12-04', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17358, 211, 1, '2025-12-04', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17359, 215, 1, '2025-12-04', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17360, 196, 1, '2025-12-04', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17361, 201, 1, '2025-12-04', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17362, 213, 1, '2025-12-04', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17363, 222, 1, '2025-12-04', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17364, 226, 1, '2025-12-04', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17365, 218, 1, '2025-12-04', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17366, 220, 1, '2025-12-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17367, 223, 1, '2025-12-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17368, 194, 1, '2025-12-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17369, 195, 1, '2025-12-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17370, 217, 1, '2025-12-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17371, 203, 1, '2025-12-04', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17372, 231, 1, '2025-12-04', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17373, 238, 1, '2025-12-04', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17374, 235, 1, '2025-12-04', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17375, 207, 1, '2025-12-04', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17376, 221, 1, '2025-12-04', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17377, 193, 1, '2025-12-04', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17378, 227, 1, '2025-12-05', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17379, 225, 1, '2025-12-05', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17380, 196, 1, '2025-12-05', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17381, 210, 1, '2025-12-05', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17382, 215, 1, '2025-12-05', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17383, 224, 1, '2025-12-05', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17384, 204, 1, '2025-12-05', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17385, 206, 1, '2025-12-05', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17386, 200, 1, '2025-12-05', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17387, 221, 1, '2025-12-05', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17388, 229, 1, '2025-12-05', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17389, 222, 1, '2025-12-05', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17390, 226, 1, '2025-12-05', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17391, 207, 1, '2025-12-05', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17392, 232, 1, '2025-12-05', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17393, 214, 1, '2025-12-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17394, 195, 1, '2025-12-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17395, 198, 1, '2025-12-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17396, 219, 1, '2025-12-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17397, 230, 1, '2025-12-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17398, 216, 1, '2025-12-05', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17399, 231, 1, '2025-12-05', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17400, 236, 1, '2025-12-05', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17401, 202, 1, '2025-12-05', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17402, 220, 1, '2025-12-05', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17403, 201, 1, '2025-12-05', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17404, 213, 1, '2025-12-05', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17405, 204, 1, '2025-12-06', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17406, 215, 1, '2025-12-06', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17407, 198, 1, '2025-12-06', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17408, 211, 1, '2025-12-06', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17409, 219, 1, '2025-12-06', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17410, 222, 1, '2025-12-06', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17411, 218, 1, '2025-12-06', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17412, 206, 1, '2025-12-06', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17413, 201, 1, '2025-12-06', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17414, 217, 1, '2025-12-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17415, 235, 1, '2025-12-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17416, 205, 1, '2025-12-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17417, 216, 1, '2025-12-06', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17418, 207, 1, '2025-12-06', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17419, 228, 1, '2025-12-06', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17420, 225, 1, '2025-12-06', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17421, 228, 1, '2025-12-07', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17422, 234, 1, '2025-12-07', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17423, 195, 1, '2025-12-07', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17424, 223, 1, '2025-12-07', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17425, 214, 1, '2025-12-07', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17426, 198, 1, '2025-12-07', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17427, 208, 1, '2025-12-07', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17428, 218, 1, '2025-12-07', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17429, 211, 1, '2025-12-07', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17430, 200, 1, '2025-12-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17431, 225, 1, '2025-12-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17432, 238, 1, '2025-12-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17433, 236, 1, '2025-12-07', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17434, 237, 1, '2025-12-07', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17435, 221, 1, '2025-12-07', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17436, 219, 1, '2025-12-07', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17437, 211, 1, '2025-12-08', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17438, 208, 1, '2025-12-08', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17439, 196, 1, '2025-12-08', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17440, 218, 1, '2025-12-08', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17441, 219, 1, '2025-12-08', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17442, 202, 1, '2025-12-08', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17443, 228, 1, '2025-12-08', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17444, 229, 1, '2025-12-08', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17445, 197, 1, '2025-12-08', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17446, 207, 1, '2025-12-08', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17447, 238, 1, '2025-12-08', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17448, 234, 1, '2025-12-08', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17449, 213, 1, '2025-12-08', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17450, 195, 1, '2025-12-08', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17451, 206, 1, '2025-12-08', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17452, 203, 1, '2025-12-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17453, 212, 1, '2025-12-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17454, 198, 1, '2025-12-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17455, 232, 1, '2025-12-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17456, 222, 1, '2025-12-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17457, 215, 1, '2025-12-08', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17458, 217, 1, '2025-12-08', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17459, 193, 1, '2025-12-08', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17460, 201, 1, '2025-12-08', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17461, 199, 1, '2025-12-08', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17462, 239, 1, '2025-12-08', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17463, 233, 1, '2025-12-08', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17464, 204, 1, '2025-12-09', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17465, 208, 1, '2025-12-09', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17466, 195, 1, '2025-12-09', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17467, 232, 1, '2025-12-09', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17468, 234, 1, '2025-12-09', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17469, 213, 1, '2025-12-09', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17470, 222, 1, '2025-12-09', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17471, 197, 1, '2025-12-09', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17472, 237, 1, '2025-12-09', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17473, 209, 1, '2025-12-09', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17474, 218, 1, '2025-12-09', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17475, 203, 1, '2025-12-09', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17476, 215, 1, '2025-12-09', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17477, 207, 1, '2025-12-09', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17478, 226, 1, '2025-12-09', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17479, 229, 1, '2025-12-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17480, 228, 1, '2025-12-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17481, 201, 1, '2025-12-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17482, 235, 1, '2025-12-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17483, 224, 1, '2025-12-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17484, 199, 1, '2025-12-09', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17485, 238, 1, '2025-12-09', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17486, 233, 1, '2025-12-09', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17487, 231, 1, '2025-12-09', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17488, 225, 1, '2025-12-09', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17489, 221, 1, '2025-12-09', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17490, 219, 1, '2025-12-09', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17491, 238, 1, '2025-12-10', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17492, 226, 1, '2025-12-10', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17493, 203, 1, '2025-12-10', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17494, 200, 1, '2025-12-10', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17495, 215, 1, '2025-12-10', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17496, 208, 1, '2025-12-10', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17497, 197, 1, '2025-12-10', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17498, 194, 1, '2025-12-10', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17499, 231, 1, '2025-12-10', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17500, 222, 1, '2025-12-10', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17501, 223, 1, '2025-12-10', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17502, 211, 1, '2025-12-10', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17503, 205, 1, '2025-12-10', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17504, 239, 1, '2025-12-10', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17505, 199, 1, '2025-12-10', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17506, 196, 1, '2025-12-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17507, 232, 1, '2025-12-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17508, 202, 1, '2025-12-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17509, 207, 1, '2025-12-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17510, 193, 1, '2025-12-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17511, 195, 1, '2025-12-10', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17512, 204, 1, '2025-12-10', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17513, 217, 1, '2025-12-10', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17514, 201, 1, '2025-12-10', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17515, 234, 1, '2025-12-10', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17516, 212, 1, '2025-12-10', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17517, 237, 1, '2025-12-10', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17518, 206, 1, '2025-12-11', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17519, 220, 1, '2025-12-11', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17520, 216, 1, '2025-12-11', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17521, 236, 1, '2025-12-11', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17522, 200, 1, '2025-12-11', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17523, 210, 1, '2025-12-11', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17524, 219, 1, '2025-12-11', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17525, 228, 1, '2025-12-11', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17526, 229, 1, '2025-12-11', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17527, 231, 1, '2025-12-11', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17528, 212, 1, '2025-12-11', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17529, 238, 1, '2025-12-11', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17530, 193, 1, '2025-12-11', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17531, 194, 1, '2025-12-11', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17532, 196, 1, '2025-12-11', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17533, 223, 1, '2025-12-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17534, 235, 1, '2025-12-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17535, 239, 1, '2025-12-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17536, 230, 1, '2025-12-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17537, 226, 1, '2025-12-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17538, 224, 1, '2025-12-11', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17539, 199, 1, '2025-12-11', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17540, 233, 1, '2025-12-11', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17541, 202, 1, '2025-12-11', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17542, 198, 1, '2025-12-11', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17543, 217, 1, '2025-12-11', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17544, 221, 1, '2025-12-11', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17545, 220, 1, '2025-12-12', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17546, 212, 1, '2025-12-12', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17547, 232, 1, '2025-12-12', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17548, 236, 1, '2025-12-12', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17549, 216, 1, '2025-12-12', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17550, 235, 1, '2025-12-12', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17551, 213, 1, '2025-12-12', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17552, 209, 1, '2025-12-12', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17553, 237, 1, '2025-12-12', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17554, 207, 1, '2025-12-12', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17555, 204, 1, '2025-12-12', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17556, 198, 1, '2025-12-12', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17557, 239, 1, '2025-12-12', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17558, 194, 1, '2025-12-12', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17559, 206, 1, '2025-12-12', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17560, 193, 1, '2025-12-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17561, 205, 1, '2025-12-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17562, 223, 1, '2025-12-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17563, 221, 1, '2025-12-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17564, 210, 1, '2025-12-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17565, 214, 1, '2025-12-12', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17566, 201, 1, '2025-12-12', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17567, 225, 1, '2025-12-12', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17568, 199, 1, '2025-12-12', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17569, 229, 1, '2025-12-12', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17570, 228, 1, '2025-12-12', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17571, 224, 1, '2025-12-12', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17572, 195, 1, '2025-12-13', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17573, 221, 1, '2025-12-13', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17574, 205, 1, '2025-12-13', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17575, 235, 1, '2025-12-13', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17576, 206, 1, '2025-12-13', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17577, 219, 1, '2025-12-13', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17578, 214, 1, '2025-12-13', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17579, 211, 1, '2025-12-13', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17580, 227, 1, '2025-12-13', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17581, 217, 1, '2025-12-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17582, 194, 1, '2025-12-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17583, 197, 1, '2025-12-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17584, 207, 1, '2025-12-13', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17585, 209, 1, '2025-12-13', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17586, 236, 1, '2025-12-13', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17587, 222, 1, '2025-12-13', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17588, 231, 1, '2025-12-14', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17589, 237, 1, '2025-12-14', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17590, 236, 1, '2025-12-14', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17591, 207, 1, '2025-12-14', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17592, 224, 1, '2025-12-14', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17593, 204, 1, '2025-12-14', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17594, 201, 1, '2025-12-14', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17595, 205, 1, '2025-12-14', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17596, 222, 1, '2025-12-14', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17597, 239, 1, '2025-12-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17598, 221, 1, '2025-12-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17599, 229, 1, '2025-12-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17600, 197, 1, '2025-12-14', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17601, 226, 1, '2025-12-14', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17602, 218, 1, '2025-12-14', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17603, 199, 1, '2025-12-14', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17604, 231, 1, '2025-12-15', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17605, 211, 1, '2025-12-15', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17606, 195, 1, '2025-12-15', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17607, 203, 1, '2025-12-15', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17608, 230, 1, '2025-12-15', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17609, 199, 1, '2025-12-15', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17610, 212, 1, '2025-12-15', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17611, 233, 1, '2025-12-15', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17612, 226, 1, '2025-12-15', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17613, 220, 1, '2025-12-15', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17614, 197, 1, '2025-12-15', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17615, 208, 1, '2025-12-15', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17616, 235, 1, '2025-12-15', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17617, 215, 1, '2025-12-15', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17618, 232, 1, '2025-12-15', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17619, 224, 1, '2025-12-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17620, 207, 1, '2025-12-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17621, 214, 1, '2025-12-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17622, 221, 1, '2025-12-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17623, 201, 1, '2025-12-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17624, 217, 1, '2025-12-15', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17625, 228, 1, '2025-12-15', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17626, 234, 1, '2025-12-15', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17627, 202, 1, '2025-12-15', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17628, 227, 1, '2025-12-15', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17629, 213, 1, '2025-12-15', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17630, 198, 1, '2025-12-15', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17631, 211, 1, '2025-12-16', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17632, 229, 1, '2025-12-16', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17633, 235, 1, '2025-12-16', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17634, 231, 1, '2025-12-16', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17635, 238, 1, '2025-12-16', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17636, 234, 1, '2025-12-16', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17637, 214, 1, '2025-12-16', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17638, 237, 1, '2025-12-16', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17639, 208, 1, '2025-12-16', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17640, 219, 1, '2025-12-16', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17641, 193, 1, '2025-12-16', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17642, 215, 1, '2025-12-16', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17643, 227, 1, '2025-12-16', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17644, 204, 1, '2025-12-16', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17645, 223, 1, '2025-12-16', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17646, 199, 1, '2025-12-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17647, 205, 1, '2025-12-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17648, 221, 1, '2025-12-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17649, 239, 1, '2025-12-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17650, 228, 1, '2025-12-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17651, 236, 1, '2025-12-16', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17652, 203, 1, '2025-12-16', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17653, 230, 1, '2025-12-16', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17654, 200, 1, '2025-12-16', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17655, 209, 1, '2025-12-16', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17656, 217, 1, '2025-12-16', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17657, 225, 1, '2025-12-16', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17658, 233, 1, '2025-12-17', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17659, 201, 1, '2025-12-17', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17660, 222, 1, '2025-12-17', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17661, 238, 1, '2025-12-17', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17662, 223, 1, '2025-12-17', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17663, 210, 1, '2025-12-17', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17664, 194, 1, '2025-12-17', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17665, 196, 1, '2025-12-17', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17666, 203, 1, '2025-12-17', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17667, 209, 1, '2025-12-17', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17668, 200, 1, '2025-12-17', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17669, 231, 1, '2025-12-17', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17670, 197, 1, '2025-12-17', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17671, 208, 1, '2025-12-17', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17672, 227, 1, '2025-12-17', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17673, 206, 1, '2025-12-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17674, 230, 1, '2025-12-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17675, 193, 1, '2025-12-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17676, 237, 1, '2025-12-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17677, 229, 1, '2025-12-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17678, 236, 1, '2025-12-17', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17679, 216, 1, '2025-12-17', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17680, 219, 1, '2025-12-17', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17681, 198, 1, '2025-12-17', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17682, 226, 1, '2025-12-17', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17683, 217, 1, '2025-12-17', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17684, 232, 1, '2025-12-17', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17685, 216, 1, '2025-12-18', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17686, 225, 1, '2025-12-18', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17687, 201, 1, '2025-12-18', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17688, 228, 1, '2025-12-18', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17689, 231, 1, '2025-12-18', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17690, 200, 1, '2025-12-18', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17691, 222, 1, '2025-12-18', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17692, 239, 1, '2025-12-18', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17693, 204, 1, '2025-12-18', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17694, 219, 1, '2025-12-18', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17695, 218, 1, '2025-12-18', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17696, 212, 1, '2025-12-18', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17697, 236, 1, '2025-12-18', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17698, 229, 1, '2025-12-18', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17699, 230, 1, '2025-12-18', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17700, 193, 1, '2025-12-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17701, 213, 1, '2025-12-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17702, 221, 1, '2025-12-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17703, 205, 1, '2025-12-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17704, 197, 1, '2025-12-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17705, 226, 1, '2025-12-18', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17706, 224, 1, '2025-12-18', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17707, 199, 1, '2025-12-18', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17708, 227, 1, '2025-12-18', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17709, 235, 1, '2025-12-18', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17710, 210, 1, '2025-12-18', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17711, 233, 1, '2025-12-18', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17712, 195, 1, '2025-12-19', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17713, 200, 1, '2025-12-19', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17714, 217, 1, '2025-12-19', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17715, 201, 1, '2025-12-19', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17716, 216, 1, '2025-12-19', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17717, 194, 1, '2025-12-19', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17718, 213, 1, '2025-12-19', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17719, 239, 1, '2025-12-19', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17720, 229, 1, '2025-12-19', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17721, 208, 1, '2025-12-19', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17722, 228, 1, '2025-12-19', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17723, 224, 1, '2025-12-19', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17724, 198, 1, '2025-12-19', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17725, 223, 1, '2025-12-19', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17726, 199, 1, '2025-12-19', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17727, 218, 1, '2025-12-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17728, 238, 1, '2025-12-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17729, 196, 1, '2025-12-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17730, 232, 1, '2025-12-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17731, 225, 1, '2025-12-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17732, 237, 1, '2025-12-19', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17733, 233, 1, '2025-12-19', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17734, 236, 1, '2025-12-19', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17735, 205, 1, '2025-12-19', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17736, 204, 1, '2025-12-19', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17737, 221, 1, '2025-12-19', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17738, 206, 1, '2025-12-19', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17739, 219, 1, '2025-12-20', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17740, 206, 1, '2025-12-20', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17741, 212, 1, '2025-12-20', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17742, 239, 1, '2025-12-20', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17743, 223, 1, '2025-12-20', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17744, 213, 1, '2025-12-20', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17745, 216, 1, '2025-12-20', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17746, 214, 1, '2025-12-20', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17747, 198, 1, '2025-12-20', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17748, 203, 1, '2025-12-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17749, 226, 1, '2025-12-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17750, 207, 1, '2025-12-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17751, 218, 1, '2025-12-20', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17752, 195, 1, '2025-12-20', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17753, 209, 1, '2025-12-20', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17754, 234, 1, '2025-12-20', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17755, 210, 1, '2025-12-21', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17756, 204, 1, '2025-12-21', 'morning', 0, '2024', '2025-11-30 02:27:08', 1);
INSERT INTO `schedules` (`id`, `user_id`, `department_id`, `schedule_date`, `shift_type`, `is_future_schedule`, `planned_month`, `created_at`, `is_holiday`) VALUES
(17757, 217, 1, '2025-12-21', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17758, 222, 1, '2025-12-21', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17759, 239, 1, '2025-12-21', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17760, 202, 1, '2025-12-21', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17761, 209, 1, '2025-12-21', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17762, 196, 1, '2025-12-21', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17763, 227, 1, '2025-12-21', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17764, 207, 1, '2025-12-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17765, 223, 1, '2025-12-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17766, 195, 1, '2025-12-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17767, 232, 1, '2025-12-21', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17768, 212, 1, '2025-12-21', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17769, 220, 1, '2025-12-21', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17770, 205, 1, '2025-12-21', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17771, 205, 1, '2025-12-22', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17772, 235, 1, '2025-12-22', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17773, 230, 1, '2025-12-22', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17774, 220, 1, '2025-12-22', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17775, 204, 1, '2025-12-22', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17776, 218, 1, '2025-12-22', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17777, 195, 1, '2025-12-22', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17778, 213, 1, '2025-12-22', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17779, 233, 1, '2025-12-22', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17780, 222, 1, '2025-12-22', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17781, 196, 1, '2025-12-22', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17782, 214, 1, '2025-12-22', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17783, 239, 1, '2025-12-22', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17784, 206, 1, '2025-12-22', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17785, 223, 1, '2025-12-22', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17786, 219, 1, '2025-12-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17787, 237, 1, '2025-12-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17788, 234, 1, '2025-12-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17789, 203, 1, '2025-12-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17790, 193, 1, '2025-12-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17791, 231, 1, '2025-12-22', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17792, 209, 1, '2025-12-22', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17793, 228, 1, '2025-12-22', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17794, 217, 1, '2025-12-22', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17795, 197, 1, '2025-12-22', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17796, 199, 1, '2025-12-22', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17797, 208, 1, '2025-12-22', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17798, 205, 1, '2025-12-23', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17799, 224, 1, '2025-12-23', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17800, 222, 1, '2025-12-23', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17801, 234, 1, '2025-12-23', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17802, 238, 1, '2025-12-23', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17803, 218, 1, '2025-12-23', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17804, 207, 1, '2025-12-23', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17805, 196, 1, '2025-12-23', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17806, 231, 1, '2025-12-23', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17807, 239, 1, '2025-12-23', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17808, 197, 1, '2025-12-23', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17809, 235, 1, '2025-12-23', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17810, 201, 1, '2025-12-23', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17811, 223, 1, '2025-12-23', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17812, 203, 1, '2025-12-23', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17813, 228, 1, '2025-12-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17814, 212, 1, '2025-12-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17815, 209, 1, '2025-12-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17816, 198, 1, '2025-12-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17817, 208, 1, '2025-12-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17818, 230, 1, '2025-12-23', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17819, 219, 1, '2025-12-23', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17820, 233, 1, '2025-12-23', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17821, 225, 1, '2025-12-23', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17822, 217, 1, '2025-12-23', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17823, 194, 1, '2025-12-23', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17824, 211, 1, '2025-12-23', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17825, 228, 1, '2025-12-24', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17826, 211, 1, '2025-12-24', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17827, 198, 1, '2025-12-24', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17828, 214, 1, '2025-12-24', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17829, 238, 1, '2025-12-24', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17830, 217, 1, '2025-12-24', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17831, 239, 1, '2025-12-24', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17832, 236, 1, '2025-12-24', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17833, 219, 1, '2025-12-24', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17834, 193, 1, '2025-12-24', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17835, 202, 1, '2025-12-24', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17836, 225, 1, '2025-12-24', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17837, 195, 1, '2025-12-24', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17838, 226, 1, '2025-12-24', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17839, 223, 1, '2025-12-24', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17840, 233, 1, '2025-12-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17841, 204, 1, '2025-12-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17842, 216, 1, '2025-12-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17843, 213, 1, '2025-12-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17844, 205, 1, '2025-12-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17845, 208, 1, '2025-12-24', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17846, 235, 1, '2025-12-24', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17847, 220, 1, '2025-12-24', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17848, 222, 1, '2025-12-24', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17849, 209, 1, '2025-12-24', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17850, 203, 1, '2025-12-24', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17851, 231, 1, '2025-12-24', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17852, 210, 1, '2025-12-25', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17853, 238, 1, '2025-12-25', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17854, 237, 1, '2025-12-25', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17855, 205, 1, '2025-12-25', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17856, 222, 1, '2025-12-25', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17857, 202, 1, '2025-12-25', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17858, 216, 1, '2025-12-25', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17859, 228, 1, '2025-12-25', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17860, 206, 1, '2025-12-25', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17861, 195, 1, '2025-12-25', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17862, 196, 1, '2025-12-25', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17863, 218, 1, '2025-12-25', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17864, 225, 1, '2025-12-25', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17865, 224, 1, '2025-12-25', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17866, 203, 1, '2025-12-25', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17867, 193, 1, '2025-12-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17868, 211, 1, '2025-12-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17869, 215, 1, '2025-12-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17870, 231, 1, '2025-12-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17871, 194, 1, '2025-12-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17872, 234, 1, '2025-12-25', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17873, 201, 1, '2025-12-25', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17874, 221, 1, '2025-12-25', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17875, 214, 1, '2025-12-25', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17876, 227, 1, '2025-12-25', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17877, 204, 1, '2025-12-25', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17878, 223, 1, '2025-12-25', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17879, 223, 1, '2025-12-26', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17880, 218, 1, '2025-12-26', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17881, 239, 1, '2025-12-26', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17882, 221, 1, '2025-12-26', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17883, 216, 1, '2025-12-26', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17884, 214, 1, '2025-12-26', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17885, 204, 1, '2025-12-26', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17886, 198, 1, '2025-12-26', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17887, 200, 1, '2025-12-26', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17888, 227, 1, '2025-12-26', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17889, 212, 1, '2025-12-26', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17890, 201, 1, '2025-12-26', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17891, 193, 1, '2025-12-26', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17892, 225, 1, '2025-12-26', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17893, 203, 1, '2025-12-26', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17894, 238, 1, '2025-12-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17895, 215, 1, '2025-12-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17896, 231, 1, '2025-12-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17897, 195, 1, '2025-12-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17898, 208, 1, '2025-12-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17899, 230, 1, '2025-12-26', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17900, 226, 1, '2025-12-26', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17901, 199, 1, '2025-12-26', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17902, 205, 1, '2025-12-26', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17903, 232, 1, '2025-12-26', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17904, 197, 1, '2025-12-26', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17905, 219, 1, '2025-12-26', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17906, 237, 1, '2025-12-27', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17907, 203, 1, '2025-12-27', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17908, 200, 1, '2025-12-27', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17909, 228, 1, '2025-12-27', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17910, 204, 1, '2025-12-27', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17911, 201, 1, '2025-12-27', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17912, 227, 1, '2025-12-27', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17913, 224, 1, '2025-12-27', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17914, 214, 1, '2025-12-27', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17915, 219, 1, '2025-12-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17916, 233, 1, '2025-12-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17917, 195, 1, '2025-12-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17918, 226, 1, '2025-12-27', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17919, 225, 1, '2025-12-27', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17920, 220, 1, '2025-12-27', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17921, 232, 1, '2025-12-27', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17922, 209, 1, '2025-12-28', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17923, 213, 1, '2025-12-28', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17924, 220, 1, '2025-12-28', 'morning', 0, '2024', '2025-11-30 02:27:08', 1),
(17925, 233, 1, '2025-12-28', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17926, 222, 1, '2025-12-28', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17927, 230, 1, '2025-12-28', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17928, 221, 1, '2025-12-28', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17929, 224, 1, '2025-12-28', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17930, 202, 1, '2025-12-28', 'night', 0, '2024', '2025-11-30 02:27:08', 1),
(17931, 210, 1, '2025-12-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17932, 195, 1, '2025-12-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17933, 218, 1, '2025-12-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 1),
(17934, 234, 1, '2025-12-28', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17935, 196, 1, '2025-12-28', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17936, 237, 1, '2025-12-28', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17937, 193, 1, '2025-12-28', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 1),
(17938, 220, 1, '2025-12-29', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17939, 215, 1, '2025-12-29', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17940, 196, 1, '2025-12-29', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17941, 226, 1, '2025-12-29', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17942, 218, 1, '2025-12-29', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17943, 197, 1, '2025-12-29', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17944, 212, 1, '2025-12-29', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17945, 209, 1, '2025-12-29', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17946, 216, 1, '2025-12-29', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17947, 233, 1, '2025-12-29', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17948, 222, 1, '2025-12-29', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17949, 238, 1, '2025-12-29', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17950, 234, 1, '2025-12-29', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17951, 201, 1, '2025-12-29', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17952, 229, 1, '2025-12-29', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17953, 203, 1, '2025-12-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17954, 198, 1, '2025-12-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17955, 193, 1, '2025-12-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17956, 211, 1, '2025-12-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17957, 214, 1, '2025-12-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17958, 221, 1, '2025-12-29', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17959, 205, 1, '2025-12-29', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17960, 217, 1, '2025-12-29', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17961, 204, 1, '2025-12-29', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17962, 195, 1, '2025-12-29', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17963, 208, 1, '2025-12-29', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17964, 199, 1, '2025-12-29', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17965, 236, 1, '2025-12-30', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17966, 200, 1, '2025-12-30', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17967, 210, 1, '2025-12-30', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17968, 237, 1, '2025-12-30', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17969, 216, 1, '2025-12-30', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17970, 218, 1, '2025-12-30', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17971, 231, 1, '2025-12-30', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17972, 235, 1, '2025-12-30', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17973, 193, 1, '2025-12-30', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17974, 203, 1, '2025-12-30', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17975, 195, 1, '2025-12-30', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17976, 223, 1, '2025-12-30', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17977, 215, 1, '2025-12-30', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17978, 211, 1, '2025-12-30', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17979, 209, 1, '2025-12-30', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(17980, 207, 1, '2025-12-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17981, 213, 1, '2025-12-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17982, 201, 1, '2025-12-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17983, 234, 1, '2025-12-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17984, 212, 1, '2025-12-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17985, 220, 1, '2025-12-30', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17986, 197, 1, '2025-12-30', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17987, 238, 1, '2025-12-30', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17988, 224, 1, '2025-12-30', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17989, 194, 1, '2025-12-30', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17990, 227, 1, '2025-12-30', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17991, 202, 1, '2025-12-30', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(17992, 209, 1, '2025-12-31', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17993, 203, 1, '2025-12-31', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17994, 235, 1, '2025-12-31', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17995, 194, 1, '2025-12-31', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17996, 213, 1, '2025-12-31', 'morning', 0, '2024', '2025-11-30 02:27:08', 0),
(17997, 193, 1, '2025-12-31', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17998, 195, 1, '2025-12-31', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(17999, 208, 1, '2025-12-31', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18000, 206, 1, '2025-12-31', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18001, 197, 1, '2025-12-31', 'afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18002, 216, 1, '2025-12-31', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(18003, 231, 1, '2025-12-31', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(18004, 198, 1, '2025-12-31', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(18005, 234, 1, '2025-12-31', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(18006, 207, 1, '2025-12-31', 'night', 0, '2024', '2025-11-30 02:27:08', 0),
(18007, 196, 1, '2025-12-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18008, 200, 1, '2025-12-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18009, 222, 1, '2025-12-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18010, 211, 1, '2025-12-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18011, 199, 1, '2025-12-31', 'morning_afternoon', 0, '2024', '2025-11-30 02:27:08', 0),
(18012, 233, 1, '2025-12-31', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18013, 218, 1, '2025-12-31', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18014, 224, 1, '2025-12-31', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18015, 238, 1, '2025-12-31', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18016, 201, 1, '2025-12-31', 'morning_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18017, 229, 1, '2025-12-31', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18018, 225, 1, '2025-12-31', 'afternoon_night', 0, '2024', '2025-11-30 02:27:08', 0),
(18019, 207, 1, '2025-11-01', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18020, 225, 1, '2025-11-01', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18021, 233, 1, '2025-11-01', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18022, 212, 1, '2025-11-01', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18023, 220, 1, '2025-11-01', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18024, 223, 1, '2025-11-01', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18025, 229, 1, '2025-11-01', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18026, 232, 1, '2025-11-01', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18027, 200, 1, '2025-11-01', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18028, 213, 1, '2025-11-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18029, 215, 1, '2025-11-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18030, 219, 1, '2025-11-01', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18031, 194, 1, '2025-11-01', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18032, 210, 1, '2025-11-01', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18033, 216, 1, '2025-11-01', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18034, 230, 1, '2025-11-01', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18035, 198, 1, '2025-11-02', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18036, 210, 1, '2025-11-02', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18037, 205, 1, '2025-11-02', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18038, 239, 1, '2025-11-02', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18039, 200, 1, '2025-11-02', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18040, 199, 1, '2025-11-02', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18041, 218, 1, '2025-11-02', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18042, 203, 1, '2025-11-02', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18043, 213, 1, '2025-11-02', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18044, 227, 1, '2025-11-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18045, 236, 1, '2025-11-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18046, 220, 1, '2025-11-02', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18047, 235, 1, '2025-11-02', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18048, 207, 1, '2025-11-02', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18049, 234, 1, '2025-11-02', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18050, 232, 1, '2025-11-02', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18051, 198, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18052, 226, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18053, 218, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18054, 227, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18055, 200, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18056, 217, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18057, 214, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18058, 213, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18059, 197, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18060, 236, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18061, 199, 1, '2025-11-03', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18062, 232, 1, '2025-11-03', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18063, 203, 1, '2025-11-03', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18064, 235, 1, '2025-11-03', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18065, 225, 1, '2025-11-03', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18066, 234, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18067, 210, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18068, 222, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18069, 207, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18070, 220, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18071, 238, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18072, 216, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18073, 206, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18074, 231, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18075, 224, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18076, 237, 1, '2025-11-03', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18077, 196, 1, '2025-11-03', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18078, 227, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18079, 199, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18080, 200, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18081, 193, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18082, 212, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18083, 234, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18084, 205, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18085, 203, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18086, 214, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18087, 220, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18088, 206, 1, '2025-11-04', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18089, 201, 1, '2025-11-04', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18090, 217, 1, '2025-11-04', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18091, 239, 1, '2025-11-04', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18092, 215, 1, '2025-11-04', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18093, 211, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18094, 216, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18095, 198, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18096, 231, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18097, 235, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18098, 210, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18099, 238, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18100, 207, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18101, 218, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18102, 228, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18103, 233, 1, '2025-11-04', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18104, 195, 1, '2025-11-04', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18105, 239, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18106, 198, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18107, 238, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18108, 225, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18109, 212, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18110, 223, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18111, 209, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18112, 211, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18113, 236, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18114, 232, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18115, 222, 1, '2025-11-05', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18116, 204, 1, '2025-11-05', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18117, 221, 1, '2025-11-05', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18118, 227, 1, '2025-11-05', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18119, 216, 1, '2025-11-05', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18120, 208, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18121, 193, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18122, 201, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18123, 230, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18124, 199, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18125, 195, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18126, 203, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18127, 200, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18128, 231, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18129, 233, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18130, 210, 1, '2025-11-05', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18131, 214, 1, '2025-11-05', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18132, 230, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18133, 229, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18134, 236, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18135, 227, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18136, 221, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18137, 219, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18138, 196, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18139, 198, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18140, 194, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18141, 204, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18142, 216, 1, '2025-11-06', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18143, 224, 1, '2025-11-06', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18144, 214, 1, '2025-11-06', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18145, 222, 1, '2025-11-06', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18146, 213, 1, '2025-11-06', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18147, 208, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18148, 201, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18149, 210, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18150, 199, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18151, 197, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18152, 209, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18153, 237, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18154, 207, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18155, 205, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18156, 231, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18157, 238, 1, '2025-11-06', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18158, 220, 1, '2025-11-06', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18159, 211, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18160, 225, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18161, 222, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18162, 237, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18163, 199, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18164, 215, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18165, 203, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18166, 209, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18167, 194, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18168, 193, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18169, 218, 1, '2025-11-07', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18170, 236, 1, '2025-11-07', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18171, 224, 1, '2025-11-07', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18172, 226, 1, '2025-11-07', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18173, 198, 1, '2025-11-07', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18174, 234, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18175, 204, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18176, 196, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18177, 201, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18178, 232, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18179, 235, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18180, 219, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18181, 227, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18182, 223, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18183, 200, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18184, 195, 1, '2025-11-07', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18185, 233, 1, '2025-11-07', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18186, 216, 1, '2025-11-08', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18187, 195, 1, '2025-11-08', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18188, 221, 1, '2025-11-08', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18189, 196, 1, '2025-11-08', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18190, 234, 1, '2025-11-08', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18191, 205, 1, '2025-11-08', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18192, 239, 1, '2025-11-08', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18193, 230, 1, '2025-11-08', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18194, 220, 1, '2025-11-08', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18195, 197, 1, '2025-11-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18196, 213, 1, '2025-11-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18197, 215, 1, '2025-11-08', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18198, 232, 1, '2025-11-08', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18199, 229, 1, '2025-11-08', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18200, 211, 1, '2025-11-08', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18201, 227, 1, '2025-11-08', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18202, 203, 1, '2025-11-09', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18203, 212, 1, '2025-11-09', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18204, 193, 1, '2025-11-09', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18205, 230, 1, '2025-11-09', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18206, 218, 1, '2025-11-09', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18207, 214, 1, '2025-11-09', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18208, 231, 1, '2025-11-09', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18209, 197, 1, '2025-11-09', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18210, 201, 1, '2025-11-09', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18211, 204, 1, '2025-11-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18212, 238, 1, '2025-11-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18213, 234, 1, '2025-11-09', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18214, 227, 1, '2025-11-09', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18215, 194, 1, '2025-11-09', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18216, 215, 1, '2025-11-09', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18217, 220, 1, '2025-11-09', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18218, 218, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18219, 210, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18220, 211, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18221, 196, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18222, 220, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18223, 229, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18224, 219, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18225, 239, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18226, 198, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18227, 197, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18228, 224, 1, '2025-11-10', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18229, 199, 1, '2025-11-10', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18230, 206, 1, '2025-11-10', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18231, 193, 1, '2025-11-10', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18232, 232, 1, '2025-11-10', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18233, 207, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18234, 195, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18235, 231, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18236, 233, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18237, 227, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18238, 214, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18239, 194, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18240, 200, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18241, 236, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18242, 203, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18243, 230, 1, '2025-11-10', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18244, 228, 1, '2025-11-10', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18245, 229, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18246, 236, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18247, 208, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18248, 214, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18249, 228, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18250, 230, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18251, 205, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18252, 204, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18253, 210, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18254, 202, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18255, 199, 1, '2025-11-11', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18256, 233, 1, '2025-11-11', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18257, 215, 1, '2025-11-11', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18258, 203, 1, '2025-11-11', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18259, 226, 1, '2025-11-11', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18260, 221, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18261, 212, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18262, 238, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18263, 235, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18264, 216, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18265, 197, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18266, 206, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18267, 231, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18268, 232, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18269, 201, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18270, 224, 1, '2025-11-11', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18271, 218, 1, '2025-11-11', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18272, 224, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18273, 214, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18274, 194, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18275, 213, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18276, 221, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18277, 209, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18278, 205, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18279, 222, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18280, 231, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18281, 232, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18282, 200, 1, '2025-11-12', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18283, 228, 1, '2025-11-12', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18284, 226, 1, '2025-11-12', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18285, 199, 1, '2025-11-12', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18286, 233, 1, '2025-11-12', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18287, 208, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18288, 211, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18289, 225, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18290, 212, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18291, 201, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18292, 216, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18293, 193, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18294, 202, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18295, 206, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18296, 195, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18297, 237, 1, '2025-11-12', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18298, 227, 1, '2025-11-12', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18299, 213, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18300, 201, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18301, 227, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18302, 222, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18303, 230, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18304, 204, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18305, 231, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18306, 219, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18307, 234, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18308, 195, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18309, 233, 1, '2025-11-13', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18310, 209, 1, '2025-11-13', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18311, 198, 1, '2025-11-13', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18312, 206, 1, '2025-11-13', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18313, 232, 1, '2025-11-13', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18314, 237, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18315, 214, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18316, 218, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18317, 225, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18318, 224, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18319, 235, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18320, 236, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18321, 216, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18322, 220, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18323, 205, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18324, 207, 1, '2025-11-13', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18325, 221, 1, '2025-11-13', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18326, 204, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18327, 211, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18328, 206, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18329, 210, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18330, 218, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18331, 193, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18332, 227, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18333, 214, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18334, 225, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18335, 203, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18336, 212, 1, '2025-11-14', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18337, 205, 1, '2025-11-14', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18338, 235, 1, '2025-11-14', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18339, 233, 1, '2025-11-14', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18340, 216, 1, '2025-11-14', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18341, 208, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18342, 194, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18343, 239, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18344, 196, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18345, 237, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18346, 236, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18347, 217, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18348, 226, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18349, 224, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18350, 201, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18351, 215, 1, '2025-11-14', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18352, 207, 1, '2025-11-14', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18353, 214, 1, '2025-11-15', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18354, 202, 1, '2025-11-15', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18355, 237, 1, '2025-11-15', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18356, 212, 1, '2025-11-15', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18357, 238, 1, '2025-11-15', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18358, 215, 1, '2025-11-15', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18359, 230, 1, '2025-11-15', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18360, 208, 1, '2025-11-15', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18361, 228, 1, '2025-11-15', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18362, 223, 1, '2025-11-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18363, 200, 1, '2025-11-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18364, 206, 1, '2025-11-15', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18365, 201, 1, '2025-11-15', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18366, 226, 1, '2025-11-15', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18367, 197, 1, '2025-11-15', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18368, 204, 1, '2025-11-15', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18369, 203, 1, '2025-11-16', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18370, 195, 1, '2025-11-16', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18371, 212, 1, '2025-11-16', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18372, 225, 1, '2025-11-16', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18373, 213, 1, '2025-11-16', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18374, 198, 1, '2025-11-16', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1);
INSERT INTO `schedules` (`id`, `user_id`, `department_id`, `schedule_date`, `shift_type`, `is_future_schedule`, `planned_month`, `created_at`, `is_holiday`) VALUES
(18375, 228, 1, '2025-11-16', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18376, 239, 1, '2025-11-16', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18377, 236, 1, '2025-11-16', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18378, 232, 1, '2025-11-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18379, 193, 1, '2025-11-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18380, 206, 1, '2025-11-16', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18381, 223, 1, '2025-11-16', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18382, 214, 1, '2025-11-16', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18383, 201, 1, '2025-11-16', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18384, 231, 1, '2025-11-16', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18385, 227, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18386, 234, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18387, 218, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18388, 208, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18389, 201, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18390, 229, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18391, 200, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18392, 203, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18393, 194, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18394, 199, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18395, 224, 1, '2025-11-17', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18396, 212, 1, '2025-11-17', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18397, 209, 1, '2025-11-17', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18398, 215, 1, '2025-11-17', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18399, 196, 1, '2025-11-17', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18400, 211, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18401, 195, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18402, 223, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18403, 235, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18404, 198, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18405, 206, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18406, 202, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18407, 197, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18408, 193, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18409, 213, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18410, 225, 1, '2025-11-17', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18411, 239, 1, '2025-11-17', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18412, 211, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18413, 202, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18414, 219, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18415, 197, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18416, 224, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18417, 200, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18418, 221, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18419, 217, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18420, 203, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18421, 222, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18422, 207, 1, '2025-11-18', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18423, 238, 1, '2025-11-18', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18424, 196, 1, '2025-11-18', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18425, 208, 1, '2025-11-18', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18426, 214, 1, '2025-11-18', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18427, 231, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18428, 229, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18429, 206, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18430, 235, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18431, 233, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18432, 201, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18433, 230, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18434, 223, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18435, 216, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18436, 209, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18437, 239, 1, '2025-11-18', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18438, 205, 1, '2025-11-18', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18439, 230, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18440, 194, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18441, 216, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18442, 195, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18443, 239, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18444, 217, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18445, 223, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18446, 234, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18447, 214, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18448, 235, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18449, 225, 1, '2025-11-19', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18450, 202, 1, '2025-11-19', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18451, 210, 1, '2025-11-19', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18452, 212, 1, '2025-11-19', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18453, 231, 1, '2025-11-19', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18454, 204, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18455, 236, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18456, 213, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18457, 228, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18458, 211, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18459, 196, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18460, 233, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18461, 229, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18462, 232, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18463, 220, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18464, 238, 1, '2025-11-19', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18465, 215, 1, '2025-11-19', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18466, 234, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18467, 198, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18468, 213, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18469, 223, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18470, 194, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18471, 221, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18472, 216, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18473, 222, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18474, 215, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18475, 228, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18476, 209, 1, '2025-11-20', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18477, 206, 1, '2025-11-20', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18478, 218, 1, '2025-11-20', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18479, 237, 1, '2025-11-20', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18480, 232, 1, '2025-11-20', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18481, 225, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18482, 201, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18483, 235, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18484, 210, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18485, 219, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18486, 224, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18487, 214, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18488, 233, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18489, 204, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18490, 220, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18491, 217, 1, '2025-11-20', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18492, 231, 1, '2025-11-20', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18493, 216, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18494, 219, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18495, 234, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18496, 197, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18497, 198, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18498, 227, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18499, 235, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18500, 209, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18501, 200, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18502, 210, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18503, 193, 1, '2025-11-21', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18504, 239, 1, '2025-11-21', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18505, 204, 1, '2025-11-21', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18506, 214, 1, '2025-11-21', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18507, 237, 1, '2025-11-21', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18508, 222, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18509, 199, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18510, 223, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18511, 231, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18512, 230, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18513, 218, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18514, 196, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18515, 203, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18516, 201, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18517, 217, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18518, 233, 1, '2025-11-21', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18519, 228, 1, '2025-11-21', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18520, 203, 1, '2025-11-22', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18521, 236, 1, '2025-11-22', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18522, 207, 1, '2025-11-22', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18523, 200, 1, '2025-11-22', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18524, 228, 1, '2025-11-22', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18525, 196, 1, '2025-11-22', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18526, 231, 1, '2025-11-22', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18527, 199, 1, '2025-11-22', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18528, 208, 1, '2025-11-22', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18529, 233, 1, '2025-11-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18530, 225, 1, '2025-11-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18531, 201, 1, '2025-11-22', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18532, 235, 1, '2025-11-22', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18533, 195, 1, '2025-11-22', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18534, 206, 1, '2025-11-22', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18535, 217, 1, '2025-11-22', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18536, 208, 1, '2025-11-23', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18537, 211, 1, '2025-11-23', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18538, 202, 1, '2025-11-23', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18539, 227, 1, '2025-11-23', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18540, 233, 1, '2025-11-23', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18541, 223, 1, '2025-11-23', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18542, 222, 1, '2025-11-23', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18543, 220, 1, '2025-11-23', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18544, 217, 1, '2025-11-23', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18545, 215, 1, '2025-11-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18546, 234, 1, '2025-11-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18547, 205, 1, '2025-11-23', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18548, 197, 1, '2025-11-23', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18549, 199, 1, '2025-11-23', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18550, 195, 1, '2025-11-23', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18551, 225, 1, '2025-11-23', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18552, 218, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18553, 210, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18554, 203, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18555, 212, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18556, 195, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18557, 209, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18558, 220, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18559, 213, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18560, 234, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18561, 230, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18562, 214, 1, '2025-11-24', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18563, 194, 1, '2025-11-24', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18564, 196, 1, '2025-11-24', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18565, 232, 1, '2025-11-24', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18566, 201, 1, '2025-11-24', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18567, 215, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18568, 204, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18569, 193, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18570, 227, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18571, 225, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18572, 229, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18573, 216, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18574, 228, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18575, 237, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18576, 222, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18577, 206, 1, '2025-11-24', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18578, 198, 1, '2025-11-24', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18579, 231, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18580, 229, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18581, 204, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18582, 199, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18583, 201, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18584, 236, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18585, 210, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18586, 235, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18587, 211, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18588, 222, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18589, 202, 1, '2025-11-25', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18590, 205, 1, '2025-11-25', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18591, 223, 1, '2025-11-25', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18592, 213, 1, '2025-11-25', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18593, 217, 1, '2025-11-25', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18594, 232, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18595, 237, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18596, 207, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18597, 203, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18598, 212, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18599, 227, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18600, 206, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18601, 234, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18602, 233, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18603, 200, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18604, 209, 1, '2025-11-25', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18605, 219, 1, '2025-11-25', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18606, 225, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18607, 213, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18608, 223, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18609, 216, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18610, 210, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18611, 195, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18612, 206, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18613, 194, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18614, 207, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18615, 198, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18616, 233, 1, '2025-11-26', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18617, 215, 1, '2025-11-26', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18618, 217, 1, '2025-11-26', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18619, 219, 1, '2025-11-26', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18620, 200, 1, '2025-11-26', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18621, 239, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18622, 209, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18623, 203, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18624, 208, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18625, 229, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18626, 220, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18627, 211, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18628, 224, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18629, 218, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18630, 205, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18631, 202, 1, '2025-11-26', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18632, 226, 1, '2025-11-26', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18633, 206, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18634, 219, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18635, 218, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18636, 211, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18637, 193, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18638, 199, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18639, 194, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18640, 226, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18641, 231, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18642, 214, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18643, 230, 1, '2025-11-27', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18644, 239, 1, '2025-11-27', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18645, 215, 1, '2025-11-27', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18646, 229, 1, '2025-11-27', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18647, 198, 1, '2025-11-27', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18648, 228, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18649, 208, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18650, 200, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18651, 225, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18652, 232, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18653, 205, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18654, 224, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18655, 223, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18656, 204, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18657, 202, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18658, 197, 1, '2025-11-27', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18659, 213, 1, '2025-11-27', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18660, 231, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18661, 196, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18662, 226, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18663, 214, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18664, 199, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-30 02:36:00', 0),
(18665, 211, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18666, 224, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18667, 198, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18668, 213, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18669, 222, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18670, 236, 1, '2025-11-28', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18671, 210, 1, '2025-11-28', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18672, 207, 1, '2025-11-28', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18673, 225, 1, '2025-11-28', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18674, 197, 1, '2025-11-28', 'night', 0, '2024', '2025-11-30 02:36:00', 0),
(18675, 217, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18676, 195, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18677, 194, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18678, 216, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18679, 228, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 0),
(18680, 193, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18681, 201, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18682, 232, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18683, 233, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18684, 223, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18685, 203, 1, '2025-11-28', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18686, 202, 1, '2025-11-28', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 0),
(18687, 212, 1, '2025-11-29', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18688, 235, 1, '2025-11-29', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18689, 239, 1, '2025-11-29', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18690, 208, 1, '2025-11-29', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18691, 232, 1, '2025-11-29', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18692, 204, 1, '2025-11-29', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18693, 209, 1, '2025-11-29', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18694, 226, 1, '2025-11-29', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18695, 224, 1, '2025-11-29', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18696, 219, 1, '2025-11-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18697, 231, 1, '2025-11-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18698, 221, 1, '2025-11-29', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18699, 193, 1, '2025-11-29', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18700, 199, 1, '2025-11-29', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18701, 201, 1, '2025-11-29', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18702, 194, 1, '2025-11-29', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18703, 205, 1, '2025-11-30', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18704, 213, 1, '2025-11-30', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18705, 228, 1, '2025-11-30', 'morning', 0, '2024', '2025-11-30 02:36:00', 1),
(18706, 214, 1, '2025-11-30', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18707, 235, 1, '2025-11-30', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18708, 196, 1, '2025-11-30', 'afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18709, 236, 1, '2025-11-30', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18710, 237, 1, '2025-11-30', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18711, 224, 1, '2025-11-30', 'night', 0, '2024', '2025-11-30 02:36:00', 1),
(18712, 230, 1, '2025-11-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18713, 221, 1, '2025-11-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18714, 193, 1, '2025-11-30', 'morning_afternoon', 0, '2024', '2025-11-30 02:36:00', 1),
(18715, 229, 1, '2025-11-30', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18716, 202, 1, '2025-11-30', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18717, 234, 1, '2025-11-30', 'morning_night', 0, '2024', '2025-11-30 02:36:00', 1),
(18718, 212, 1, '2025-11-30', 'afternoon_night', 0, '2024', '2025-11-30 02:36:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_rules`
--

CREATE TABLE `schedule_rules` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `rule_name` varchar(255) NOT NULL,
  `rule_description` text DEFAULT NULL,
  `morning_count` int(11) DEFAULT 0,
  `afternoon_count` int(11) DEFAULT 0,
  `night_count` int(11) DEFAULT 0,
  `day_count` int(11) DEFAULT 0,
  `night_shift_count` int(11) DEFAULT 0,
  `morning_afternoon_count` int(11) DEFAULT 0,
  `morning_night_count` int(11) DEFAULT 0,
  `afternoon_night_count` int(11) DEFAULT 0,
  `max_concurrent_leave` int(11) DEFAULT 3,
  `work_days_before_leave` int(11) DEFAULT 5,
  `monthly_leave_days` int(11) DEFAULT 8,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `holiday_morning_count` int(11) DEFAULT 5,
  `holiday_afternoon_count` int(11) DEFAULT 5,
  `holiday_night_count` int(11) DEFAULT 5,
  `holiday_day_count` int(11) DEFAULT 0,
  `holiday_night_shift_count` int(11) DEFAULT 0,
  `holiday_morning_afternoon_count` int(11) DEFAULT 3,
  `holiday_morning_night_count` int(11) DEFAULT 3,
  `holiday_afternoon_night_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_rules`
--

INSERT INTO `schedule_rules` (`id`, `department_id`, `rule_name`, `rule_description`, `morning_count`, `afternoon_count`, `night_count`, `day_count`, `night_shift_count`, `morning_afternoon_count`, `morning_night_count`, `afternoon_night_count`, `max_concurrent_leave`, `work_days_before_leave`, `monthly_leave_days`, `created_by`, `created_at`, `updated_at`, `holiday_morning_count`, `holiday_afternoon_count`, `holiday_night_count`, `holiday_day_count`, `holiday_night_shift_count`, `holiday_morning_afternoon_count`, `holiday_morning_night_count`, `holiday_afternoon_night_count`) VALUES
(1, 1, 'กฏแผนกบุคคล', 'กฏระเบียบพื้นฐานสำหรับแผนกบุคคล', 5, 5, 5, 0, 0, 5, 5, 2, 3, 5, 8, 2, '2025-11-14 11:53:01', '2025-11-28 05:33:00', 3, 3, 3, 0, 0, 3, 3, 1),
(2, 2, 'กฏแผนกบัญชี', 'กฏระเบียบสำหรับแผนกบัญชี', 1, 1, 0, 1, 0, 0, 0, 0, 2, 5, 7, 3, '2025-11-14 11:53:01', '2025-11-14 11:53:01', 5, 5, 5, 0, 0, 3, 3, 0),
(3, 3, 'กฏแผนกไอที', 'กฏระเบียบสำหรับแผนกไอที', 1, 1, 1, 0, 0, 0, 0, 0, 2, 4, 6, 4, '2025-11-14 11:53:01', '2025-11-14 11:53:01', 5, 5, 5, 0, 0, 3, 3, 0),
(4, 1, 'กฏแผนกบุคคล', 'กฏระเบียบพื้นฐานสำหรับแผนกบุคคล', 5, 5, 5, 0, 0, 5, 5, 2, 3, 5, 8, 2, '2025-11-19 03:44:55', '2025-11-28 05:33:00', 3, 3, 3, 0, 0, 3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `id` int(11) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_blocked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_events`
--

INSERT INTO `security_events` (`id`, `event_type`, `description`, `ip_address`, `user_agent`, `severity`, `is_blocked`, `created_at`) VALUES
(1, 'failed_login', 'Failed login attempt for username: hradmin from IP: 127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'medium', 0, '2025-11-28 01:40:10'),
(2, 'failed_login', 'Failed login attempt for username: hradmin from IP: 127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'medium', 0, '2025-11-28 01:40:16'),
(3, 'failed_login', 'Failed login attempt for username: hradmin from IP: 127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'medium', 0, '2025-11-28 01:40:18'),
(4, 'failed_login', 'Failed login attempt for username: hradmin from IP: 127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'medium', 0, '2025-11-28 01:41:20'),
(5, 'failed_login', 'Failed login attempt for username: adminhr from IP: 127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'medium', 0, '2025-11-28 04:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `security_settings`
--

CREATE TABLE `security_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_settings`
--

INSERT INTO `security_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'max_login_attempts', '5', 'จำนวนครั้งที่ล็อกอินผิดได้ก่อนถูกบล็อก', NULL, '2025-11-14 11:48:47'),
(2, 'lockout_duration', '30', 'ระยะเวลาการบล็อก (นาที)', NULL, '2025-11-14 11:48:47'),
(3, 'session_timeout', '60', 'ระยะเวลา Session (นาที)', NULL, '2025-11-14 11:48:47'),
(4, 'password_min_length', '8', 'ความยาวรหัสผ่านขั้นต่ำ', NULL, '2025-11-14 11:48:47'),
(5, 'password_require_uppercase', '1', 'ต้องมีตัวพิมพ์ใหญ่', NULL, '2025-11-14 11:48:47'),
(6, 'password_require_lowercase', '1', 'ต้องมีตัวพิมพ์เล็ก', NULL, '2025-11-14 11:48:47'),
(7, 'password_require_numbers', '1', 'ต้องมีตัวเลข', NULL, '2025-11-14 11:48:47'),
(8, 'password_require_special_chars', '1', 'ต้องมีอักขระพิเศษ', NULL, '2025-11-14 11:48:47'),
(9, 'password_expiry_days', '90', 'อายุรหัสผ่าน (วัน)', NULL, '2025-11-14 11:48:47'),
(10, 'enable_brute_force_protection', '1', 'เปิดใช้งานการป้องกัน Brute Force', NULL, '2025-11-14 11:48:47'),
(11, 'enable_audit_log', '1', 'เปิดใช้งาน Audit Log', NULL, '2025-11-14 11:48:47'),
(12, 'enable_rate_limiting', '1', NULL, NULL, '2025-11-19 04:21:17'),
(13, 'max_requests_per_minute', '60', NULL, NULL, '2025-11-19 04:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `swap_history`
--

CREATE TABLE `swap_history` (
  `id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `user1_id` int(11) DEFAULT NULL,
  `user2_id` int(11) DEFAULT NULL,
  `original_schedule_id` int(11) DEFAULT NULL,
  `target_schedule_id` int(11) DEFAULT NULL,
  `swap_date` date NOT NULL,
  `status` enum('completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `swap_requests`
--

CREATE TABLE `swap_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `original_schedule_id` int(11) DEFAULT NULL,
  `target_schedule_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `level` enum('user','admin','super_admin') DEFAULT 'user',
  `employee_level` int(11) DEFAULT 1,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 0,
  `password_changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `department_id`, `level`, `employee_level`, `email`, `phone`, `is_active`, `last_login`, `login_attempts`, `last_login_attempt`, `is_locked`, `locked_until`, `must_change_password`, `password_changed_at`, `created_by`, `created_at`, `updated_at`) VALUES
(192, 'superadmin', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Super Admin', 1, 'super_admin', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(193, 'hradmin', '$2b$12$E6oMW/14Q1l9dVsijbU0be0/5fdUb3qs2cync1IYoVVp2ZDwqi7T.', 'HR Admin', 1, 'admin', 3, NULL, NULL, 1, '2025-11-30 02:48:23', 0, '2025-11-28 01:41:20', 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-30 02:48:23'),
(194, 'emp_lv3_01', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 01', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(195, 'emp_lv3_02', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 02', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(196, 'emp_lv3_03', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 03', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(197, 'emp_lv3_04', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 04', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(198, 'emp_lv3_05', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 05', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(199, 'emp_lv3_06', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L3 - 06', 1, 'user', 3, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(200, 'emp_lv2_01', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 01', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(201, 'emp_lv2_02', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 02', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(202, 'emp_lv2_03', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 03', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(203, 'emp_lv2_04', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 04', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(204, 'emp_lv2_05', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 05', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(205, 'emp_lv2_06', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 06', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(206, 'emp_lv2_07', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 07', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(207, 'emp_lv2_08', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 08', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(208, 'emp_lv2_09', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 09', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(209, 'emp_lv2_10', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 10', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(210, 'emp_lv2_11', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 11', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(211, 'emp_lv2_12', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 12', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(212, 'emp_lv2_13', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 13', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(213, 'emp_lv2_14', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 14', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(214, 'emp_lv2_15', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 15', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(215, 'emp_lv2_16', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 16', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(216, 'emp_lv2_17', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 17', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(217, 'emp_lv2_18', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 18', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(218, 'emp_lv2_19', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 19', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(219, 'emp_lv2_20', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L2 - 20', 1, 'user', 2, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(220, 'emp_lv1_01', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 01', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(221, 'emp_lv1_02', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 02', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(222, 'emp_lv1_03', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 03', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(223, 'emp_lv1_04', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 04', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(224, 'emp_lv1_05', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 05', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(225, 'emp_lv1_06', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 06', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(226, 'emp_lv1_07', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 07', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(227, 'emp_lv1_08', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 08', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(228, 'emp_lv1_09', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 09', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(229, 'emp_lv1_10', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 10', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(230, 'emp_lv1_11', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 11', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(231, 'emp_lv1_12', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 12', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(232, 'emp_lv1_13', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 13', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(233, 'emp_lv1_14', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 14', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(234, 'emp_lv1_15', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 15', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(235, 'emp_lv1_16', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 16', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(236, 'emp_lv1_17', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 17', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(237, 'emp_lv1_18', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 18', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(238, 'emp_lv1_19', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 19', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29'),
(239, 'emp_lv1_20', '$2y$10$EixZaYVK1fsbw1ZfbX3OXehvscgQ/2n6EJD7BqXc9EwP6KDAdAm8m', 'Employee L1 - 20', 1, 'user', 1, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-28 01:22:29', NULL, '2025-11-28 01:22:29', '2025-11-28 01:22:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_edit_history`
--

CREATE TABLE `user_edit_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `action_type` enum('create','update','delete') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_level_rules`
--
ALTER TABLE `employee_level_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_department_level` (`department_id`,`employee_level`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- Indexes for table `future_leave_requests`
--
ALTER TABLE `future_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `future_swap_requests`
--
ALTER TABLE `future_swap_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `target_user_id` (`target_user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `holiday_rules`
--
ALTER TABLE `holiday_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_department_holiday` (`department_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `holiday_settings`
--
ALTER TABLE `holiday_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_identifier` (`identifier`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `schedule_rules`
--
ALTER TABLE `schedule_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `swap_history`
--
ALTER TABLE `swap_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`),
  ADD KEY `original_schedule_id` (`original_schedule_id`),
  ADD KEY `target_schedule_id` (`target_schedule_id`);

--
-- Indexes for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `target_user_id` (`target_user_id`),
  ADD KEY `original_schedule_id` (`original_schedule_id`),
  ADD KEY `target_schedule_id` (`target_schedule_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `user_edit_history`
--
ALTER TABLE `user_edit_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `edited_by` (`edited_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_level_rules`
--
ALTER TABLE `employee_level_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `future_leave_requests`
--
ALTER TABLE `future_leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `future_swap_requests`
--
ALTER TABLE `future_swap_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday_rules`
--
ALTER TABLE `holiday_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `holiday_settings`
--
ALTER TABLE `holiday_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18719;

--
-- AUTO_INCREMENT for table `schedule_rules`
--
ALTER TABLE `schedule_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `swap_history`
--
ALTER TABLE `swap_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `swap_requests`
--
ALTER TABLE `swap_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `user_edit_history`
--
ALTER TABLE `user_edit_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_level_rules`
--
ALTER TABLE `employee_level_rules`
  ADD CONSTRAINT `employee_level_rules_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `employee_level_rules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `future_leave_requests`
--
ALTER TABLE `future_leave_requests`
  ADD CONSTRAINT `future_leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `future_leave_requests_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `future_leave_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `future_swap_requests`
--
ALTER TABLE `future_swap_requests`
  ADD CONSTRAINT `future_swap_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `future_swap_requests_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `future_swap_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `holiday_rules`
--
ALTER TABLE `holiday_rules`
  ADD CONSTRAINT `holiday_rules_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `holiday_rules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `holiday_settings`
--
ALTER TABLE `holiday_settings`
  ADD CONSTRAINT `holiday_settings_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `holiday_settings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `leave_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `schedule_rules`
--
ALTER TABLE `schedule_rules`
  ADD CONSTRAINT `schedule_rules_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `schedule_rules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD CONSTRAINT `security_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `swap_history`
--
ALTER TABLE `swap_history`
  ADD CONSTRAINT `swap_history_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `swap_requests` (`id`),
  ADD CONSTRAINT `swap_history_ibfk_2` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `swap_history_ibfk_3` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `swap_history_ibfk_4` FOREIGN KEY (`original_schedule_id`) REFERENCES `schedules` (`id`),
  ADD CONSTRAINT `swap_history_ibfk_5` FOREIGN KEY (`target_schedule_id`) REFERENCES `schedules` (`id`);

--
-- Constraints for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD CONSTRAINT `swap_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `swap_requests_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `swap_requests_ibfk_3` FOREIGN KEY (`original_schedule_id`) REFERENCES `schedules` (`id`),
  ADD CONSTRAINT `swap_requests_ibfk_4` FOREIGN KEY (`target_schedule_id`) REFERENCES `schedules` (`id`),
  ADD CONSTRAINT `swap_requests_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_edit_history`
--
ALTER TABLE `user_edit_history`
  ADD CONSTRAINT `user_edit_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_edit_history_ibfk_2` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
