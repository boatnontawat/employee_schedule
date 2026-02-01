-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 04:46 AM
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
(1, 1, 'login', 'User logged in successfully', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'users', 1, NULL, NULL, 'low', '2025-11-14 11:53:01'),
(2, 2, 'user_create', 'Created new user: user.hr3', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'users', 4, NULL, NULL, 'medium', '2025-11-14 11:53:01'),
(3, 4, 'schedule_update', 'Updated schedule for user.it1', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'schedules', 7, NULL, NULL, 'low', '2025-11-14 11:53:01'),
(4, 1, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 1, NULL, NULL, 'low', '2025-11-14 12:00:35'),
(5, 1, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 1, NULL, NULL, 'low', '2025-11-14 12:00:57'),
(6, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 2, NULL, NULL, 'low', '2025-11-14 12:01:04'),
(7, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 2, NULL, NULL, 'low', '2025-11-19 03:29:21'),
(8, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":2,\"afternoon_count\":2,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:37:40'),
(9, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:37:44'),
(10, 2, 'schedule_generate', 'Generated 120 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":120}', 'medium', '2025-11-19 03:37:49'),
(11, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:45:45'),
(12, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:46:05'),
(13, 2, 'schedule_generate', 'Generated 210 schedules for 2025-11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedules', NULL, NULL, '{\"month\":\"2025-11\",\"count\":210}', 'medium', '2025-11-19 03:46:16'),
(14, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:52:44'),
(15, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', 'medium', '2025-11-19 03:57:10'),
(16, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 2, NULL, NULL, 'low', '2025-11-20 03:08:06'),
(17, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3,\"level_rules\":{\"level1\":{\"min\":1,\"max\":3},\"level2\":{\"min\":1,\"max\":2},\"level3\":{\"min\":1,\"max\":1}},\"holiday_rules\":{\"morning_count\":2,\"afternoon_count\":2,\"night_count\":2,\"min_level\":2}}', 'medium', '2025-11-20 03:40:48'),
(18, 2, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 2, NULL, NULL, 'low', '2025-11-20 03:41:32'),
(19, 5, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 5, NULL, NULL, 'low', '2025-11-20 03:41:38'),
(20, 5, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 5, NULL, NULL, 'low', '2025-11-20 03:42:37'),
(21, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'users', 2, NULL, NULL, 'low', '2025-11-20 03:42:44'),
(22, 2, 'schedule_rules_update', 'update schedule rules for department ID: 1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'schedule_rules', 1, '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3}', '{\"morning_count\":1,\"afternoon_count\":1,\"max_concurrent_leave\":3,\"level_rules\":{\"level1\":{\"min\":1,\"max\":3},\"level2\":{\"min\":1,\"max\":2},\"level3\":{\"min\":1,\"max\":1}},\"holiday_rules\":{\"morning_count\":2,\"afternoon_count\":2,\"night_count\":2,\"min_level\":2}}', 'medium', '2025-11-20 03:43:37');

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
(1, 1, 1, 1, 3, 2, '2025-11-20 03:40:48', '2025-11-20 03:43:37'),
(2, 1, 2, 1, 2, 2, '2025-11-20 03:40:48', '2025-11-20 03:43:37'),
(3, 1, 3, 1, 1, 2, '2025-11-20 03:40:48', '2025-11-20 03:43:37');

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

--
-- Dumping data for table `future_leave_requests`
--

INSERT INTO `future_leave_requests` (`id`, `user_id`, `department_id`, `request_type`, `start_date`, `end_date`, `reason`, `status`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'vacation', '2025-12-14', '2025-12-18', 'ไปเที่ยวต่างจังหวัด', 'pending', NULL, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(2, 8, 3, 'personal', '2025-12-29', '2025-12-31', 'ธุระส่วนตัว', 'approved', NULL, '2025-11-14 11:53:01', '2025-11-14 11:53:01');

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
(1, 1, 2, 2, 2, 2, 2, '2025-11-20 03:40:48', '2025-11-20 03:43:37');

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

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `department_id`, `request_type`, `start_date`, `end_date`, `reason`, `medical_certificate`, `status`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'sick_leave', '2025-11-16', '2025-11-17', 'ไม่สบายเป็นไข้', NULL, 'approved', 1, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(2, 7, 3, '', '2025-11-19', '2025-11-21', 'ไปเที่ยวพักผ่อน', NULL, 'pending', NULL, '2025-11-14 11:53:01', '2025-11-14 11:53:01');

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
(4, 5, 2, '2025-11-14', 'morning', 0, '2024', '2025-11-14 11:53:01', 0),
(5, 6, 2, '2025-11-14', 'day', 0, '2024', '2025-11-14 11:53:01', 0),
(6, 7, 3, '2025-11-14', 'morning', 0, '2024', '2025-11-14 11:53:01', 0),
(7, 8, 3, '2025-11-14', 'afternoon', 0, '2024', '2025-11-14 11:53:01', 0),
(8, 9, 3, '2025-11-14', 'night', 0, '2024', '2025-11-14 11:53:01', 0),
(162, 6, 1, '2025-11-01', 'morning', 0, '2024', '2025-11-19 03:46:14', 0),
(163, 89, 1, '2025-11-01', 'afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(164, 7, 1, '2025-11-01', 'night', 0, '2024', '2025-11-19 03:46:14', 0),
(165, 78, 1, '2025-11-01', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(166, 95, 1, '2025-11-01', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(167, 5, 1, '2025-11-01', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(168, 83, 1, '2025-11-01', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(169, 82, 1, '2025-11-02', 'morning', 0, '2024', '2025-11-19 03:46:14', 0),
(170, 93, 1, '2025-11-02', 'afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(171, 87, 1, '2025-11-02', 'night', 0, '2024', '2025-11-19 03:46:14', 0),
(172, 2, 1, '2025-11-02', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(173, 5, 1, '2025-11-02', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(174, 81, 1, '2025-11-02', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(175, 74, 1, '2025-11-02', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(176, 7, 1, '2025-11-03', 'morning', 0, '2024', '2025-11-19 03:46:14', 0),
(177, 73, 1, '2025-11-03', 'afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(178, 87, 1, '2025-11-03', 'night', 0, '2024', '2025-11-19 03:46:14', 0),
(179, 2, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(180, 84, 1, '2025-11-03', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(181, 77, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(182, 76, 1, '2025-11-03', 'morning_night', 0, '2024', '2025-11-19 03:46:14', 0),
(183, 74, 1, '2025-11-04', 'morning', 0, '2024', '2025-11-19 03:46:14', 0),
(184, 94, 1, '2025-11-04', 'afternoon', 0, '2024', '2025-11-19 03:46:14', 0),
(185, 88, 1, '2025-11-04', 'night', 0, '2024', '2025-11-19 03:46:14', 0),
(186, 85, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(187, 89, 1, '2025-11-04', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(188, 83, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(189, 2, 1, '2025-11-04', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(190, 7, 1, '2025-11-05', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(191, 90, 1, '2025-11-05', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(192, 81, 1, '2025-11-05', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(193, 75, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(194, 78, 1, '2025-11-05', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(195, 77, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(196, 92, 1, '2025-11-05', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(197, 87, 1, '2025-11-06', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(198, 75, 1, '2025-11-06', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(199, 73, 1, '2025-11-06', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(200, 2, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(201, 77, 1, '2025-11-06', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(202, 88, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(203, 78, 1, '2025-11-06', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(204, 92, 1, '2025-11-07', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(205, 84, 1, '2025-11-07', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(206, 73, 1, '2025-11-07', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(207, 88, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(208, 5, 1, '2025-11-07', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(209, 87, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(210, 78, 1, '2025-11-07', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(211, 95, 1, '2025-11-08', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(212, 75, 1, '2025-11-08', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(213, 90, 1, '2025-11-08', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(214, 84, 1, '2025-11-08', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(215, 96, 1, '2025-11-08', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(216, 76, 1, '2025-11-08', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(217, 7, 1, '2025-11-08', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(218, 96, 1, '2025-11-09', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(219, 76, 1, '2025-11-09', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(220, 86, 1, '2025-11-09', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(221, 7, 1, '2025-11-09', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(222, 90, 1, '2025-11-09', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(223, 83, 1, '2025-11-09', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(224, 91, 1, '2025-11-09', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(225, 82, 1, '2025-11-10', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(226, 80, 1, '2025-11-10', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(227, 7, 1, '2025-11-10', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(228, 88, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(229, 79, 1, '2025-11-10', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(230, 81, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(231, 86, 1, '2025-11-10', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(232, 77, 1, '2025-11-11', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(233, 79, 1, '2025-11-11', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(234, 74, 1, '2025-11-11', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(235, 95, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(236, 93, 1, '2025-11-11', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(237, 88, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(238, 6, 1, '2025-11-11', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(239, 2, 1, '2025-11-12', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(240, 91, 1, '2025-11-12', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(241, 86, 1, '2025-11-12', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(242, 87, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(243, 79, 1, '2025-11-12', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(244, 78, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(245, 5, 1, '2025-11-12', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(246, 83, 1, '2025-11-13', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(247, 7, 1, '2025-11-13', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(248, 80, 1, '2025-11-13', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(249, 75, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(250, 2, 1, '2025-11-13', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(251, 5, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(252, 6, 1, '2025-11-13', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(253, 91, 1, '2025-11-14', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(254, 81, 1, '2025-11-14', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(255, 84, 1, '2025-11-14', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(256, 77, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(257, 90, 1, '2025-11-14', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(258, 89, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(259, 82, 1, '2025-11-14', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(260, 2, 1, '2025-11-15', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(261, 7, 1, '2025-11-15', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(262, 93, 1, '2025-11-15', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(263, 89, 1, '2025-11-15', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(264, 86, 1, '2025-11-15', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(265, 94, 1, '2025-11-15', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(266, 84, 1, '2025-11-15', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(267, 5, 1, '2025-11-16', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(268, 75, 1, '2025-11-16', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(269, 74, 1, '2025-11-16', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(270, 96, 1, '2025-11-16', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(271, 81, 1, '2025-11-16', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(272, 94, 1, '2025-11-16', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(273, 78, 1, '2025-11-16', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(274, 76, 1, '2025-11-17', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(275, 89, 1, '2025-11-17', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(276, 95, 1, '2025-11-17', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(277, 83, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(278, 82, 1, '2025-11-17', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(279, 77, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(280, 96, 1, '2025-11-17', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(281, 79, 1, '2025-11-18', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(282, 2, 1, '2025-11-18', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(283, 95, 1, '2025-11-18', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(284, 94, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(285, 76, 1, '2025-11-18', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(286, 86, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(287, 93, 1, '2025-11-18', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(288, 91, 1, '2025-11-19', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(289, 74, 1, '2025-11-19', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(290, 82, 1, '2025-11-19', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(291, 83, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(292, 6, 1, '2025-11-19', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(293, 88, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(294, 96, 1, '2025-11-19', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(295, 2, 1, '2025-11-20', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(296, 91, 1, '2025-11-20', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(297, 75, 1, '2025-11-20', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(298, 79, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(299, 94, 1, '2025-11-20', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(300, 93, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(301, 92, 1, '2025-11-20', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(302, 88, 1, '2025-11-21', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(303, 7, 1, '2025-11-21', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(304, 95, 1, '2025-11-21', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(305, 5, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(306, 90, 1, '2025-11-21', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(307, 76, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(308, 78, 1, '2025-11-21', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(309, 84, 1, '2025-11-22', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(310, 83, 1, '2025-11-22', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(311, 74, 1, '2025-11-22', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(312, 6, 1, '2025-11-22', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(313, 87, 1, '2025-11-22', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(314, 77, 1, '2025-11-22', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(315, 78, 1, '2025-11-22', 'morning_night', 0, '2024', '2025-11-19 03:46:15', 0),
(316, 84, 1, '2025-11-23', 'morning', 0, '2024', '2025-11-19 03:46:15', 0),
(317, 93, 1, '2025-11-23', 'afternoon', 0, '2024', '2025-11-19 03:46:15', 0),
(318, 2, 1, '2025-11-23', 'night', 0, '2024', '2025-11-19 03:46:15', 0),
(319, 5, 1, '2025-11-23', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(320, 79, 1, '2025-11-23', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(321, 88, 1, '2025-11-23', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(322, 92, 1, '2025-11-23', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(323, 91, 1, '2025-11-24', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(324, 77, 1, '2025-11-24', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(325, 88, 1, '2025-11-24', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(326, 87, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(327, 85, 1, '2025-11-24', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(328, 83, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(329, 79, 1, '2025-11-24', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(330, 88, 1, '2025-11-25', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(331, 93, 1, '2025-11-25', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(332, 91, 1, '2025-11-25', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(333, 89, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(334, 85, 1, '2025-11-25', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(335, 81, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(336, 96, 1, '2025-11-25', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(337, 95, 1, '2025-11-26', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(338, 5, 1, '2025-11-26', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(339, 83, 1, '2025-11-26', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(340, 73, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(341, 7, 1, '2025-11-26', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(342, 87, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(343, 94, 1, '2025-11-26', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(344, 74, 1, '2025-11-27', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(345, 75, 1, '2025-11-27', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(346, 81, 1, '2025-11-27', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(347, 76, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(348, 84, 1, '2025-11-27', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(349, 2, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(350, 88, 1, '2025-11-27', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(351, 7, 1, '2025-11-28', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(352, 94, 1, '2025-11-28', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(353, 85, 1, '2025-11-28', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(354, 77, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(355, 80, 1, '2025-11-28', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(356, 87, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(357, 91, 1, '2025-11-28', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(358, 84, 1, '2025-11-29', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(359, 92, 1, '2025-11-29', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(360, 86, 1, '2025-11-29', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(361, 73, 1, '2025-11-29', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(362, 94, 1, '2025-11-29', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(363, 90, 1, '2025-11-29', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(364, 91, 1, '2025-11-29', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(365, 95, 1, '2025-11-30', 'morning', 0, '2024', '2025-11-19 03:46:16', 0),
(366, 77, 1, '2025-11-30', 'afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(367, 79, 1, '2025-11-30', 'night', 0, '2024', '2025-11-19 03:46:16', 0),
(368, 73, 1, '2025-11-30', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(369, 80, 1, '2025-11-30', 'morning_afternoon', 0, '2024', '2025-11-19 03:46:16', 0),
(370, 76, 1, '2025-11-30', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0),
(371, 2, 1, '2025-11-30', 'morning_night', 0, '2024', '2025-11-19 03:46:16', 0);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_rules`
--

INSERT INTO `schedule_rules` (`id`, `department_id`, `rule_name`, `rule_description`, `morning_count`, `afternoon_count`, `night_count`, `day_count`, `night_shift_count`, `morning_afternoon_count`, `morning_night_count`, `afternoon_night_count`, `max_concurrent_leave`, `work_days_before_leave`, `monthly_leave_days`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'กฏแผนกบุคคล', 'กฏระเบียบพื้นฐานสำหรับแผนกบุคคล', 1, 1, 1, 0, 1, 2, 2, 0, 3, 5, 8, 2, '2025-11-14 11:53:01', '2025-11-20 03:43:37'),
(2, 2, 'กฏแผนกบัญชี', 'กฏระเบียบสำหรับแผนกบัญชี', 1, 1, 0, 1, 0, 0, 0, 0, 2, 5, 7, 3, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(3, 3, 'กฏแผนกไอที', 'กฏระเบียบสำหรับแผนกไอที', 1, 1, 1, 0, 0, 0, 0, 0, 2, 4, 6, 4, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(4, 1, 'กฏแผนกบุคคล', 'กฏระเบียบพื้นฐานสำหรับแผนกบุคคล', 1, 1, 1, 0, 1, 2, 2, 0, 3, 5, 8, 2, '2025-11-19 03:44:55', '2025-11-20 03:43:37');

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
(1, 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 1, 'super_admin', 3, 'superadmin@company.com', '000-000-0000', 1, '2025-11-14 12:00:35', 0, NULL, 0, NULL, 0, '2025-11-14 11:48:47', 1, '2025-11-14 11:48:47', '2025-11-20 02:52:48'),
(2, 'admin.hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วรรณิดา เก่งมาก', 1, 'admin', 3, 'admin.hr@company.com', '081-222-2222', 1, '2025-11-20 03:42:44', 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 1, '2025-11-14 11:53:01', '2025-11-20 03:42:44'),
(3, 'admin.account', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อนุชา ทำงานดี', 2, 'admin', 1, 'admin.account@company.com', '081-333-3333', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 1, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(4, 'admin.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธีรภัทร สมาร์ท', 3, 'admin', 1, 'admin.it@company.com', '081-444-4444', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 1, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(5, 'user.hr1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมชาย ใจดี', 1, 'user', 3, 'user.hr1@company.com', '082-111-1111', 1, '2025-11-20 03:41:38', 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 2, '2025-11-14 11:53:01', '2025-11-20 03:41:38'),
(6, 'user.hr2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมหญิง เก่งมาก', 1, 'user', 3, 'user.hr2@company.com', '082-222-2222', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 2, '2025-11-14 11:53:01', '2025-11-20 02:53:07'),
(7, 'user.hr3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สุนิตา ใจเย็น', 1, 'user', 3, 'user.hr3@company.com', '082-333-3333', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 2, '2025-11-14 11:53:01', '2025-11-20 02:53:14'),
(8, 'user.acc1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'พัชราภรณ์ meticulous', 2, 'user', 1, 'user.acc1@company.com', '082-444-4444', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 3, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(9, 'user.acc2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'เกศราภรณ์ ละเอียด', 2, 'user', 1, 'user.acc2@company.com', '082-555-5555', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 3, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(10, 'user.it1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธนวัฒน์ เทคโน', 3, 'user', 1, 'user.it1@company.com', '082-666-6666', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 4, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(11, 'user.it2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ณัฐพล โปรแกรม', 3, 'user', 1, 'user.it2@company.com', '082-777-7777', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 4, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(12, 'user.it3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อรุณี เน็ตเวิร์ก', 3, 'user', 1, 'user.it3@company.com', '082-888-8888', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-14 11:53:01', 4, '2025-11-14 11:53:01', '2025-11-14 11:53:01'),
(73, 'user.hr6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วรัญญา กระตือรือร้น', 1, 'user', 2, 'user.hr6@company.com', '082-666-6666', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:28'),
(74, 'user.hr7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธนภัทร ตรงเวลา', 1, 'user', 2, 'user.hr7@company.com', '082-777-7777', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:33'),
(75, 'user.hr8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อรวรรณ อบอุ่น', 1, 'user', 2, 'user.hr8@company.com', '082-888-8888', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:35'),
(76, 'user.hr9', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'พงศกร มีน้ำใจ', 1, 'user', 2, 'user.hr9@company.com', '082-999-9999', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:37'),
(77, 'user.hr10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สิริยากร เก่งรอบด้าน', 1, 'user', 1, 'user.hr10@company.com', '083-111-1111', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(78, 'user.hr11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ชัยวัฒน์ มุ่งมั่น', 1, 'user', 1, 'user.hr11@company.com', '083-222-2222', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(79, 'user.hr12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'เกตุวดี อ่อนโยน', 1, 'user', 1, 'user.hr12@company.com', '083-333-3333', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(80, 'user.hr13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ณัฐพงศ์ เรียนรู้เร็ว', 1, 'user', 1, 'user.hr13@company.com', '083-444-4444', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(81, 'user.hr14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ปวีณา สื่อสารดี', 1, 'user', 1, 'user.hr14@company.com', '083-555-5555', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(82, 'user.hr15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อนุชิต รับผิดชอบ', 1, 'user', 2, 'user.hr15@company.com', '083-666-6666', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:40'),
(83, 'user.hr16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศิรินาถ เอาใจใส่', 1, 'user', 3, 'user.hr16@company.com', '083-777-7777', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:42'),
(84, 'user.hr17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ภูริทัต ทำงานทีม', 1, 'user', 3, 'user.hr17@company.com', '083-888-8888', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:45'),
(85, 'user.hr18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'กนกวรรณ ประสานงาน', 1, 'user', 2, 'user.hr18@company.com', '083-999-9999', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-20 02:53:49'),
(86, 'user.hr19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธีรพงศ์ วิเคราะห์', 1, 'user', 1, 'user.hr19@company.com', '084-111-1111', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(87, 'user.hr20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'รัตนาภรณ์ จัดการ', 1, 'user', 1, 'user.hr20@company.com', '084-222-2222', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(88, 'user.hr21', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศักดิ์สิทธิ์ แก้ปัญหา', 1, 'user', 1, 'user.hr21@company.com', '084-333-3333', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(89, 'user.hr22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อารียา สร้างสรรค์', 1, 'user', 1, 'user.hr22@company.com', '084-444-4444', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(90, 'user.hr23', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วัฒนา พัฒนาตน', 1, 'user', 1, 'user.hr23@company.com', '084-555-5555', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(91, 'user.hr24', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'เบญจมาศ ตรงเป้า', 1, 'user', 1, 'user.hr24@company.com', '084-666-6666', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(92, 'user.hr25', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'จักรกฤษ เน้นคุณภาพ', 1, 'user', 1, 'user.hr25@company.com', '084-777-7777', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(93, 'user.hr26', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'พิมพรรณ สามัคคี', 1, 'user', 1, 'user.hr26@company.com', '084-888-8888', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(94, 'user.hr27', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อานนท์ มองการณ์ไกล', 1, 'user', 1, 'user.hr27@company.com', '084-999-9999', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(95, 'user.hr28', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วรนุช วางระบบ', 1, 'user', 1, 'user.hr28@company.com', '085-111-1111', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(96, 'user.hr29', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สิทธิโชค ประหยัด', 1, 'user', 1, 'user.hr29@company.com', '085-222-2222', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 03:44:55', 2, '2025-11-19 03:44:55', '2025-11-19 03:44:55'),
(101, 'hr.somchai', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมชาย จิตต์ดี', 1, 'user', 3, 'hr.somchai@company.com', '081-111-1111', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(102, 'hr.nongluck', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'น้องลักษณ์ ทรัพยากร', 1, 'user', 2, 'hr.nongluck@company.com', '081-111-1112', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(103, 'hr.somsak', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมศักดิ์ มนุษย์สัมพันธ์', 1, 'user', 2, 'hr.somsak@company.com', '081-111-1113', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(104, 'hr.wanida', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วานิดา เกื้อกูล', 1, 'user', 2, 'hr.wanida@company.com', '081-111-1114', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(105, 'hr.preecha', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ปรีชา เริ่มต้น', 1, 'user', 1, 'hr.preecha@company.com', '081-111-1115', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(106, 'hr.siriporn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศิริพร สดใส', 1, 'user', 1, 'hr.siriporn@company.com', '081-111-1116', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(107, 'hr.thanawat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธนวัฒน์ กระตือรือร้น', 1, 'user', 1, 'hr.thanawat@company.com', '081-111-1117', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(108, 'hr.kannika', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'กาญจนา เรียนรู้เร็ว', 1, 'user', 1, 'hr.kannika@company.com', '081-111-1118', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(109, 'hr.anutin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อนุทิน ขยันขันแข็ง', 1, 'user', 1, 'hr.anutin@company.com', '081-111-1119', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(110, 'hr.jaruwan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'จารุวรรณ ตั้งใจทำงาน', 1, 'user', 1, 'hr.jaruwan@company.com', '081-111-1120', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(111, 'hr.sirinat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศิรินาถ เอาใจใส่', 1, 'user', 3, 'hr.sirinat@company.com', '081-111-1121', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(112, 'hr.phuritat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ภูริทัต ทำงานทีม', 1, 'user', 3, 'hr.phuritat@company.com', '081-111-1122', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(113, 'hr.kanokwan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'กนกวรรณ ประสานงาน', 1, 'user', 2, 'hr.kanokwan@company.com', '081-111-1123', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(114, 'hr.anuchit', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อนุชิต รับผิดชอบ', 1, 'user', 2, 'hr.anuchit@company.com', '081-111-1124', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(115, 'hr.theerapong', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธีรพงศ์ วิเคราะห์', 1, 'user', 1, 'hr.theerapong@company.com', '081-111-1125', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(116, 'hr.rattanaporn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'รัตนาภรณ์ จัดการ', 1, 'user', 1, 'hr.rattanaporn@company.com', '081-111-1126', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(117, 'hr.saksit', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศักดิ์สิทธิ์ แก้ปัญหา', 1, 'user', 1, 'hr.saksit@company.com', '081-111-1127', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(118, 'hr.ariya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อารียา สร้างสรรค์', 1, 'user', 1, 'hr.ariya@company.com', '081-111-1128', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(119, 'hr.wattana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วัฒนา พัฒนาตน', 1, 'user', 1, 'hr.wattana@company.com', '081-111-1129', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00'),
(120, 'hr.benjamat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'เบญจมาศ ตรงเป้า', 1, 'user', 1, 'hr.benjamat@company.com', '081-111-1130', 1, NULL, 0, NULL, 0, NULL, 0, '2025-11-19 19:55:00', 2, '2025-11-19 19:55:00', '2025-11-19 19:55:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `schedule_rules`
--
ALTER TABLE `schedule_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

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
