-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 01, 2026 at 09:17 AM
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
-- Database: `exam`
--

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `is_registered` tinyint(1) NOT NULL DEFAULT 1,
  `answers` text NOT NULL DEFAULT '[]',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `status` enum('not_started','started','in_progress','completed','abandoned','rules_violation') DEFAULT 'not_started',
  `time_remaining` varchar(11) DEFAULT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `passed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `registration_id`, `student_id`, `exam_id`, `url`, `is_registered`, `answers`, `started_at`, `completed_at`, `status`, `time_remaining`, `score`, `percentage`, `passed`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 1, '2315378ed4e6dbbb7a3e11d70b4838a74fcb27e30abfb6033e078686bbdd3e569a8b6956cddf1d03b453600ef61bf46c617712c888d5cc65fdb4c140799ff940e8e8a0770b9070a8cc5b6e5c02eace1d6b6382e2d1c2c106f7bb', 1, '[{\"question_id\":\"16\",\"answer\":\"A\",\"flagged\":\"false\"},{\"question_id\":\"8\",\"answer\":\"B\",\"flagged\":\"false\"},{\"question_id\":\"4\",\"answer\":\"D\",\"flagged\":\"false\"},{\"question_id\":\"9\",\"answer\":\"C\",\"flagged\":\"false\"},{\"question_id\":\"2\",\"answer\":\"C\",\"flagged\":\"false\"}]', '2025-12-23 11:00:00', '2025-12-23 11:33:13', 'rules_violation', '01:26:50', 3.00, 3.00, 0, '2025-12-13 07:12:40', '2025-12-23 06:03:13'),
(2, 2, 15, 11, '0458e1bc6ef679f3ac0e0577c3abd3679bf2dac10683db9bdc8298c2f02d9a8b7d4da08c5954394f278750c360d2ddbf1182498a7ef300c1a0a243ded4e55fb47adf0668f56ce964093cfdd5bab375e3c7801c1975d3e7a30c49', 1, '[{\"question_id\":\"2\",\"answer\":\"D\",\"flagged\":\"false\"},{\"question_id\":\"1\",\"answer\":\"B\",\"flagged\":\"true\"},{\"question_id\":\"8\",\"answer\":\"B\",\"flagged\":\"true\"},{\"question_id\":\"4\",\"answer\":\"B\",\"flagged\":\"false\"},{\"question_id\":\"3\",\"answer\":\"D\",\"flagged\":\"false\"}]', '2025-12-23 11:21:58', '2025-12-23 11:22:08', 'rules_violation', '01:59:52', 4.00, 4.00, 0, '2025-12-13 10:28:16', '2025-12-23 05:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `exam_info`
--

CREATE TABLE `exam_info` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `code` varchar(250) NOT NULL,
  `duration` decimal(25,2) NOT NULL DEFAULT 0.00,
  `total_num_of_ques` int(4) NOT NULL,
  `total_marks` int(4) NOT NULL DEFAULT 0,
  `passing_marks` int(4) NOT NULL DEFAULT 0,
  `instructions` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `course` int(11) DEFAULT NULL,
  `module` int(11) DEFAULT NULL,
  `batch` year(4) DEFAULT NULL,
  `semi` int(2) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `published_by` int(11) NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_info`
--

INSERT INTO `exam_info` (`id`, `title`, `code`, `duration`, `total_num_of_ques`, `total_marks`, `passing_marks`, `instructions`, `status`, `course`, `module`, `batch`, `semi`, `created_by`, `published_by`, `published_at`, `created_at`) VALUES
(1, 'test 1', 'test 1', 120.00, 13, 100, 40, '', 1, NULL, NULL, NULL, 0, 5, 1, '2025-12-11 00:00:00', '2025-11-25 05:45:41'),
(11, '2nd Semi Math Exam', 'MATH_2025_SEMI_2', 120.00, 5, 100, 40, '', 1, NULL, NULL, NULL, 0, 5, 1, '2025-12-11 00:00:00', '2025-12-11 05:01:46'),
(20, '2nd Semi Math Exam', 'MATH_2025_SEMI_2', 120.00, 5, 100, 40, '', 0, NULL, NULL, NULL, 0, 5, 1, '2025-12-11 00:00:00', '2025-12-11 05:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `exam_registration`
--

CREATE TABLE `exam_registration` (
  `id` int(11) NOT NULL,
  `reg_no` varchar(25) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','in_progress','completed','canceled') DEFAULT 'registered',
  `preferred_language` varchar(10) DEFAULT 'en',
  `special_accommodations` text DEFAULT NULL,
  `receive_notifications` tinyint(1) DEFAULT 0,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `attempts_count` int(11) DEFAULT 0,
  `last_attempt_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_registration`
--

INSERT INTO `exam_registration` (`id`, `reg_no`, `exam_id`, `student_id`, `registration_date`, `status`, `preferred_language`, `special_accommodations`, `receive_notifications`, `terms_accepted`, `attempts_count`, `last_attempt_date`, `created_at`, `updated_at`) VALUES
(1, 'EX_REG_0001', 1, 15, '2025-12-13 12:42:40', 'completed', 'en', NULL, 0, 1, 1, '2025-12-23 11:00:00', '2025-12-13 07:12:40', '2025-12-23 06:03:13'),
(2, 'EX_REG_0002', 11, 15, '2025-12-13 15:58:16', 'completed', 'en', NULL, 0, 1, 2, '2025-12-23 11:21:58', '2025-12-13 10:28:16', '2025-12-23 05:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `exam_settings`
--

CREATE TABLE `exam_settings` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `schedule_type` varchar(255) NOT NULL DEFAULT 'scheduled',
  `start_time` datetime DEFAULT NULL,
  `shuffle_questions` tinyint(1) DEFAULT 0,
  `shuffle_options` tinyint(1) DEFAULT 0,
  `immediate_results` tinyint(1) DEFAULT 0,
  `retake` tinyint(1) DEFAULT 0,
  `max_attempts` int(3) DEFAULT 1,
  `enable_proctoring` tinyint(1) DEFAULT 0,
  `full_screen_mode` tinyint(1) DEFAULT 0,
  `disable_copy_paste` tinyint(1) DEFAULT 0,
  `disable_right_click` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_settings`
--

INSERT INTO `exam_settings` (`id`, `exam_id`, `schedule_type`, `start_time`, `shuffle_questions`, `shuffle_options`, `immediate_results`, `retake`, `max_attempts`, `enable_proctoring`, `full_screen_mode`, `disable_copy_paste`, `disable_right_click`, `created_at`, `updated_at`) VALUES
(1, 1, 'scheduled', '2025-12-24 11:00:00', 1, 1, 0, 1, 1, 1, 1, 1, 1, '2025-12-05 09:57:53', '2025-12-23 11:10:42'),
(13, 11, 'anytime', NULL, 1, 1, 0, 1, 2, 0, 1, 1, 1, '2025-12-11 05:47:19', '2025-12-15 08:55:19');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_ids` text DEFAULT NULL,
  `section_ids` text DEFAULT NULL,
  `question` text NOT NULL,
  `q_img` text DEFAULT NULL,
  `marks` decimal(25,2) NOT NULL,
  `answer` varchar(10) NOT NULL,
  `a` text DEFAULT NULL,
  `b` text DEFAULT NULL,
  `c` text DEFAULT NULL,
  `d` text DEFAULT NULL,
  `a_img` text DEFAULT NULL,
  `b_img` text DEFAULT NULL,
  `c_img` text DEFAULT NULL,
  `d_img` text DEFAULT NULL,
  `grid` int(2) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_ids`, `section_ids`, `question`, `q_img`, `marks`, `answer`, `a`, `b`, `c`, `d`, `a_img`, `b_img`, `c_img`, `d_img`, `grid`, `created_by`, `created_at`) VALUES
(1, '[\"1\", \"11\"]', '[\"6\", \"31\"]', 'Test Question 1', NULL, 2.00, 'B', 'Option A', 'Option B', 'Option C', 'Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-11-25 10:03:52'),
(2, '[\"1\", \"11\"]', NULL, 'Test Question 2', NULL, 2.00, 'B', 'Option A', 'Option B', 'Option C', 'Option D', NULL, NULL, NULL, NULL, 1, 1, '2025-11-25 10:07:39'),
(3, '[\"1\", \"11\"]', '[\"6\", \"31\"]', 'Test Question 3', NULL, 2.00, 'B', 'Option A', 'Option B', 'Option C', 'Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-11-25 10:08:30'),
(4, '[\"1\", \"11\"]', NULL, 'Test Question 4', NULL, 2.00, 'B', 'Option A', 'Option B', 'Option C', 'Option D', NULL, NULL, NULL, NULL, 1, 1, '2025-11-25 10:11:21'),
(8, '[\"1\", \"11\"]', '[\"6\"]', 'Test Question 5', NULL, 2.00, 'A', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 05:17:01'),
(9, '[\"1\"]', '[\"6\"]', 'Test Question 6', NULL, 2.00, 'C', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 06:32:58'),
(10, '[\"1\"]', '[\"6\"]', 'Test Question 7', NULL, 2.00, 'C', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 06:34:21'),
(11, '[\"1\"]', NULL, 'Test Question 8', NULL, 1.00, 'C', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 07:01:26'),
(12, '[\"1\"]', NULL, 'Test Question 9', NULL, 1.00, 'B', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 07:05:28'),
(13, '[\"1\"]', NULL, 'Test Question 10', NULL, 1.00, 'A', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 07:07:14'),
(14, '[\"1\"]', '[\"29\"]', 'Test Question 10', NULL, 1.00, 'C', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 07:11:20'),
(15, '[\"1\"]', '[\"29\"]', 'Test Question 11', NULL, 1.00, 'B', 'Test Option A', 'Test Option B', 'Test Option C', 'Test Option D', NULL, NULL, NULL, NULL, 2, 1, '2025-12-02 07:21:47'),
(16, '[\"1\"]', NULL, 'fwsefew', NULL, 1.00, 'A', 'sdfdsf', 'sdfsd', 'sdfsd', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:26:41'),
(17, '', NULL, 'dasdsad', NULL, 1.00, 'A', 'asdas', 'asdas', 'asdas', 'asdas', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:27:48'),
(18, NULL, NULL, 'sdfsdfds', NULL, 1.00, 'A', 'dsfsdf', 'sdfsd', 'sdfds', 'dsfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:28:12'),
(19, NULL, NULL, 'fdsadsa', NULL, 1.00, 'B', 'dasdsa', 'asdasas', 'asdas', 'asd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:28:53'),
(20, NULL, NULL, 'fsdfs', NULL, 1.00, 'B', 'sdfs', 'sdfsd', 'fdsfsd', 'dsfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:33:33'),
(21, NULL, NULL, 'hgfhf', NULL, 1.00, 'B', 'gfdgfd', 'dfgdfg', 'dfgfd', 'dfgfdg', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:34:30'),
(22, NULL, NULL, 'dsfdsf', NULL, 1.00, 'C', 'sdfsd', 'dsfdsfds', 'dsfsd', 'sdfsdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:37:49'),
(23, NULL, NULL, 'gfdggfd', NULL, 1.00, 'B', 'hgfdgf', 'gdfg', 'dfgdf', 'fdgdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 07:38:22'),
(24, NULL, NULL, 'hgjghj', NULL, 1.00, 'C', 'hgjhg', 'jhghg', 'ghjhg', 'ghjgh', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 08:16:58'),
(25, NULL, NULL, 'XZCFDF', NULL, 1.00, 'B', 'sfdgfds', 'gfdgfd', 'dfgfd', 'dfgd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 08:17:47'),
(26, NULL, NULL, 'hgjhgj', NULL, 1.00, 'B', 'hgjhg', 'ghjgh', 'ghjg', 'hgjgh', NULL, NULL, NULL, NULL, 1, 1, '2025-12-02 08:18:02'),
(27, NULL, NULL, 'gdfgf', NULL, 1.00, 'C', 'gdfgfd', 'dfgdf', 'dfgdfg', 'dfgdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 03:51:20'),
(28, NULL, NULL, 'dgfdg', NULL, 1.00, 'A', 'dfgfd', 'fdgdf', 'dfgdf', 'dfgfdg', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 03:59:32'),
(29, NULL, NULL, 'ddas', NULL, 1.00, 'B', 'dassa', 'asdsa', 'asdsad', 'asdsad', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:06:43'),
(30, NULL, NULL, 'fdsgdsf', NULL, 1.00, 'A', 'sdfsd', 'sdfsd', 'sdfds', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:07:34'),
(31, NULL, NULL, 'fdsfdfsd', NULL, 1.00, 'A', 'sdfsd', 'sdfds', 'sdfsdf', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:08:18'),
(32, NULL, NULL, 'rerf', NULL, 1.00, 'A', 'dsfsd', 'sdfs', 'sdfd', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:10:14'),
(33, NULL, NULL, 'fdsfsdf', NULL, 1.00, 'A', 'sdfsd', 'sdfsd', 'sdfsd', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:11:19'),
(34, NULL, NULL, 'fdsfdsf', NULL, 1.00, 'A', 'sdfsd', 'sdfsd', 'sdfds', 'sdfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:12:10'),
(35, NULL, NULL, 'dsadsads', NULL, 1.00, 'B', 'asdsa', 'sadsa', 'sadas', 'sadasd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:27:30'),
(36, NULL, NULL, 'yretre', NULL, 1.00, 'B', 'tret', 'ertre', 'rter', 'ertre', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 06:35:15'),
(37, NULL, NULL, 'ffsdf', NULL, 1.00, 'A', 'sdfsd', 'sdfds', 'sdfsd', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 07:48:30'),
(38, NULL, NULL, 'fsdfsd', NULL, 1.00, 'C', 'sdfsd', 'sdfsd', 'fdsd', 'sdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:02:16'),
(39, NULL, NULL, 'hhfg', NULL, 1.00, 'C', 'fhgf', 'fghfg', 'hfg', 'fghf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:12:14'),
(40, NULL, NULL, 'gfdgfd', NULL, 1.00, 'C', 'fdgfd', 'gfdgfd', 'fdgfd', 'dfgfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:19:59'),
(41, NULL, NULL, 'gdfg', NULL, 1.00, 'C', 'gdfg', 'fdg', 'gd', 'gfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:21:13'),
(42, NULL, NULL, 'dfsdds', NULL, 1.00, 'A', 'sdf', 'sdfds', 'dsfsd', 'sdfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:24:04'),
(43, NULL, NULL, 'fdsfdsf', NULL, 1.00, 'B', 'sdfsd', 'dsf', 'sdf', 'sdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:27:59'),
(44, NULL, NULL, 'dfds', NULL, 1.00, 'B', 'dsfds', 'sdfsd', 'dsfsd', 'dsfds', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:29:00'),
(45, NULL, NULL, 'bdfgf', NULL, 1.00, 'D', 'fdgfd', 'fdgfd', 'fgfd', 'dfgfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:30:23'),
(46, NULL, NULL, 'bcvb', NULL, 1.00, 'B', 'dghdfg', 'nvb', 'sdf', 'vbcb', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:31:47'),
(47, NULL, NULL, 'bcvb', NULL, 1.00, 'B', 'dghdfg', 'nvb', 'sdf', 'vbcb', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:31:47'),
(48, NULL, NULL, 'fgsdd', NULL, 1.00, 'A', 'dfsd', 'sdfsd', 'dfsd', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:32:34'),
(49, NULL, NULL, 'fsdfd', NULL, 1.00, 'A', 'fds', 'sdfds', 'dsfds', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 09:33:30'),
(50, NULL, NULL, 'asdsa', NULL, 1.00, 'B', 'dsas', 'asdsa', 'sdsa', 'sadsa', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 10:21:38'),
(51, NULL, NULL, 'gdfg', NULL, 1.00, 'C', 'fdgdf', 'gdf', 'fdgfd', 'dfgfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 10:25:39'),
(52, NULL, NULL, 'gfdgfd', NULL, 1.00, 'B', 'gfdgfd', 'fdgfd', 'dfgdf', 'dfgdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 10:38:23'),
(53, NULL, NULL, 'fsdfsd', NULL, 1.00, 'B', 'fsdf', 'sfds', 'sdfs', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 11:27:40'),
(54, NULL, NULL, 'csdfsd', NULL, 1.00, 'A', 'sdfds', 'sdfsd', 'fsdf', 'dsfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 11:31:40'),
(55, NULL, NULL, 'gfdgfgfd', NULL, 1.00, 'A', 'gdfg', 'gdfg', 'dfgdf', 'gdfg', NULL, NULL, NULL, NULL, 1, 1, '2025-12-03 11:44:24'),
(56, NULL, NULL, 'gfd', NULL, 1.00, 'C', 'gfdg', 'fdgfd', 'dfgfdgfd', 'gfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-05 04:30:15'),
(57, NULL, NULL, 'dfgfd', NULL, 1.00, 'B', 'fdgfd', 'fgdfd', 'fdgfd', 'gfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-05 04:30:31'),
(58, NULL, NULL, 'gfdgfd', NULL, 1.00, 'A', 'dfgfd', 'gdfd', 'dfg', 'fdg', NULL, NULL, NULL, NULL, 1, 1, '2025-12-05 06:55:27'),
(59, NULL, NULL, 'gfd', NULL, 1.00, 'A', 'fdg', 'fdgfd', 'dfg', 'gfd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-05 06:55:50'),
(60, NULL, NULL, 'fsdfdsd', NULL, 1.00, 'B', 'fsdf', 'sdfds', 'fsdf', 'sdfsd', NULL, NULL, NULL, NULL, 1, 1, '2025-12-11 05:08:57'),
(61, NULL, NULL, 'sdfdf', NULL, 1.00, 'B', 'dsfsd', 'dfds', 'sdf', 'sdf', NULL, NULL, NULL, NULL, 4, 1, '2025-12-11 05:11:34'),
(62, NULL, NULL, 'fsdfds', NULL, 1.00, 'A', 'fdsf', 'sdf', 'sdf', 'sdf', NULL, NULL, NULL, NULL, 2, 1, '2025-12-11 05:35:00'),
(63, NULL, NULL, 'fsdf', NULL, 1.00, 'A', 'fdsf', 'sdf', 'sdf', 'sdf', NULL, NULL, NULL, NULL, 1, 1, '2025-12-11 05:35:02'),
(64, NULL, NULL, 'gfdg', NULL, 1.00, 'A', 'dfg', 'dfg', 'fdg', 'dfg', NULL, NULL, NULL, NULL, 4, 1, '2025-12-11 05:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `s_des` text DEFAULT NULL,
  `s_s_des` text DEFAULT NULL,
  `num_of_ques` int(3) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `exam_id`, `title`, `s_des`, `s_s_des`, `num_of_ques`, `created_by`, `created_at`) VALUES
(6, 1, 'Section 1', NULL, NULL, 5, 1, '2025-12-02 11:18:48'),
(29, 1, 'Section 2', 'Test Description', 'Test Second Description', 2, 1, '2025-12-03 11:38:41'),
(31, 11, 'Section 3', 'Test Description', '', 2, 1, '2025-12-13 10:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `reg_no` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` int(16) NOT NULL,
  `username` varchar(255) NOT NULL,
  `user_group` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expire` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `reg_no`, `name`, `phone`, `username`, `user_group`, `email`, `password`, `note`, `status`, `reset_token`, `token_expire`, `created_at`, `updated_at`) VALUES
(1, 'TEC1001', 'Technical', 769104866, 'Technical', 1, 'nit@nit.lk', '$2y$10$yxmxgdwkKx./JeouE7440eWhyEmHwyKtA2gLGFnTBeO0WBluD316K', NULL, 0, '716a0e344973c3fc3de6a2fd8310d4584d1302b178ff847d3e38c8ab145bbb45', '2025-12-25 11:10:58', '2025-11-10 04:35:12', '2025-12-31 09:26:04'),
(2, 'SADMIN1002', 'Super Admin', 770000000, 'Super Admin', 2, 'sadmin@gmail.com', '$2y$10$XSHpkVodlQWIK1dcqwoxaecQvH/1FLq5KFSiaMnL8hS2DXdI1WeBm', NULL, 0, NULL, NULL, '2025-11-10 04:35:12', '2025-12-24 05:43:00'),
(3, 'ADMIN1003', 'Admin', 770000001, 'Admin', 3, 'admin@gmail.com', '$2y$10$IlvEIF9FHbKO4idOCYAie.NFQkokCRylyChihPeVr5..PGfMGYhbC', NULL, 0, NULL, NULL, '2025-11-11 05:15:21', '2025-12-24 05:43:06'),
(4, 'HOD1004', 'HOD', 770000002, 'HOD', 4, 'hod@gmail.com', '$2y$10$comQ2IKtK4Mzyu8X8kg74.N17/9WGXbZcSqcDrT0ks8dDXBDUO6Xi', NULL, 0, NULL, NULL, '2025-11-11 05:15:52', '2025-12-24 05:31:20'),
(5, 'LEC1005', 'Lecturer', 770000003, 'Lecturer', 5, 'lecturer@gmail.com', '$2y$10$kPn8ypKFeiBb.NySZKiqJubY1QXp0/x6pALTVONOZTKVuGcRTz0QK', NULL, 0, NULL, NULL, '2025-11-11 05:15:52', '2025-12-24 05:31:31'),
(6, 'STU1006', 'Student', 770000004, 'Student', 6, 'student@gmail.com', '$2y$10$YkAfCZplNZIapz2F5HWz.ud71q59USbBUmZn2Yc8PPP4.zTXwRIJC', NULL, 0, NULL, NULL, '2025-11-11 05:15:52', '2025-12-24 05:31:41'),
(7, 'PAR1007', 'Parent', 770000005, 'Parent', 7, 'parent@gmail.com', '$2y$10$QifOmKW/FcDWPEH61qZhNu8ma5ApURI8.HOyogFknfq3MnZkg79G2', NULL, 0, NULL, NULL, '2025-11-11 05:15:52', '2025-12-24 05:31:46'),
(11, 'STU1009', 'Saththiyaseelan Keyithan', 772114093, 'Keyithanb', 6, 'saththiyaseelankeyithan@gamil.com', '$2y$10$k4pxrO7HLIrE8ncvhopGz.3Qz0uxjQKu6883494QdKjmlm4xiNfEC', '', 0, NULL, NULL, '2025-12-12 11:35:28', '2025-12-24 05:32:00'),
(12, 'STU1010', 'Saththiyaseelan Keyithan', 772114093, 'keyi', 6, 'sathyseelankeyithan@gamil.com', '$2y$10$76n.80qqYVRczqJPoza3buVSrtz4TVsFfzlnAMf8mSKv3Y1EX0Tpu', '', 0, NULL, NULL, '2025-12-12 11:36:20', '2025-12-24 05:32:08'),
(13, 'STU1012', 'Saththiyaseelan Keyithan', 77211493, 'nitexist', 6, 'tech@nit.lk', '$2y$10$NVJKzuytRBbNKh8vqrJFyuRCVf.Zt2jZzccfuFdw0hYk7A6g3b7HC', '', 0, NULL, NULL, '2025-12-13 03:58:20', '2025-12-24 05:32:23'),
(14, 'STU1013', 'Saththiyaseelan Keyithan', 77211493, 'Keyith', 1, 'nit@tech.lk', '$2y$10$fuMjI2WHPRBjFwgYo6ubPO.SdRT9FLX8r.TyiaL28GsdpiVAWuUm6', '', 0, NULL, NULL, '2025-12-13 05:05:38', '2025-12-24 05:32:30'),
(15, 'STU1014', 'Saththiyaseelan Keyithan', 7622918, 'Tech', 6, 'sadmin@nit.com', '$2y$10$vPb0aSK3qdldVDSbKC1h6eKnGzue7iGKMvUi/dkgsJSc2jTt49f56', '', 0, NULL, NULL, '2025-12-13 05:09:28', '2025-12-24 05:32:35'),
(16, 'ADMIN1015', 'Saththiyaseelan Keyithan', 772020918, 'NIT_TECH', 3, 'skeyithan@gmail.com', '$2y$10$hOVQ88HUUnYki4X576L6PufQETxatBRuU6a07hj2OvwPg3c9B.wSi', '', 0, NULL, NULL, '2025-12-24 05:44:09', '2025-12-24 10:09:45'),
(17, 'ADMIN1016', 'Saththiyaseelan Keyithan', 770202918, 'deleted_17', 3, 'deleted_17_1766568827@deleted.com', '$2y$10$crKDI1Kq9nb1BqMF4fUcM.kXJRyGAbH5AsmxKe0CEoM3.vvJxX6Pu', '', 3, NULL, NULL, '2025-12-24 05:46:26', '2025-12-24 09:33:47'),
(19, 'STU1015', 'Saththiyaseelan Keyithan', 772114093, 'deleted_19', 6, 'deleted_19_1766568873@deleted.com', '$2y$10$vr2zIx3c2.jaJVnxG.iKHO5vUZm8qJZtJVRT/yUElaJVjEvm8sdZW', '', 3, NULL, NULL, '2025-12-24 07:08:24', '2025-12-24 09:34:33'),
(21, 'STU1016', 'Saththiyaseelan Keyithan', 772114093, 'Keyithan', 6, 'sathyjaseelankeyithan@gmail.com', '$2y$10$vuxhEQdn/1lzSNpmmU/iQuV54rFCjVQJ.Hg.4x17kcKrrBrFUYchi', '', 0, '640d50f1df45024df4c9ee68b509583ee98952596d60079bb512c4b9ff20e859', '2026-01-01 08:19:40', '2025-12-31 09:50:12', '2026-01-01 08:14:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `permission` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`id`, `name`, `permission`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Technical', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.create\',\'exams.edit\',\'exams.view\',\'exams.delete\',\'exams.attempt\',\'exams.all\',\'exams.my\',\'exams.manage\',\'exams.schedule\',\'exams.preview\',\'exams.register\',\'questions.create\',\'questions.view\',\'questions.edit\',\'questions.edit.my\',\'questions.delete\',\'questions.delete.my\',\'questions.bank\',\'questions.my\',\'questions.all\',\'questions.assign\',\'results.view\',\'results.publish\',\'results.all\',\'results.my\',\'results.edit\',\'results.delete\',\'results.create\',\'results.manage\',\'results.review\',\'users.manage\',\'users.create\',\'users.edit\',\'users.delete\',\'users.view\',\'users.all\',\'users.add\',\'students.manage\',\'groups.manage\',\'notifications.view\',\'notifications.send\',\'notifications.manage\',\'notifications.my\',\'notifications.all\',\'notifications.templates\',\'notifications.settings\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'profile.delete\',\'profile.delete.my\',\'settings.manage\',\'settings.advanced\']', NULL, 1, '2025-11-10 04:35:12', '2025-12-23 04:46:00'),
(2, 'Administrator', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.view\',\'exams.all\',\'exams.schedule\',\'exams.preview\',\'questions.view\',\'questions.all\',\'results.view\',\'results.publish\',\'results.all\',\'results.delete\',\'results.manage\',\'users.manage\',\'users.create\',\'users.edit\',\'users.delete\',\'users.view\',\'users.all\',\'users.add\',\'students.manage\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'profile.delete\',\'settings.manage\',\'results.review\']', NULL, 1, '2025-11-10 04:35:12', '2025-12-23 06:14:51'),
(3, 'Admin', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.view\',\'exams.all\',\'exams.manage\',\'exams.schedule\',\'exams.preview\',\'questions.view\',\'questions.all\',\'results.view\',\'results.publish\',\'results.all\',\'results.edit\',\'results.delete\',\'results.create\',\'results.manage\',\'users.manage\',\'users.create\',\'users.edit\',\'users.delete\',\'users.view\',\'users.all\',\'users.add\',\'students.manage\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'profile.delete\',\'settings.manage\',\'results.review\']', NULL, 1, '2025-11-10 10:24:07', '2025-12-23 06:26:41'),
(4, 'HOD', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.edit\',\'exams.view\',\'exams.all\',\'exams.manage\',\'exams.schedule\',\'exams.preview\',\'questions.view\',\'questions.edit\',\'questions.edit.my\',\'questions.delete\',\'questions.delete.my\',\'questions.my\',\'results.view\',\'results.publish\',\'results.all\',\'results.edit\',\'results.create\',\'results.manage\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'profile.delete.my\',\'results.review\']', NULL, 1, '2025-11-10 10:24:07', '2025-12-23 06:14:33'),
(5, 'Lecturer', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.create\',\'exams.edit\',\'exams.view\',\'exams.delete\',\'exams.all\',\'exams.my\',\'exams.manage\',\'exams.schedule\',\'exams.preview\',\'questions.create\',\'questions.my\',\'questions.delete.my\',\'questions.edit.my\',\'questions.view\',\'results.view\',\'results.all\',\'results.edit\',\'results.create\',\'results.manage\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'profile.delete.my\',\'results.review\']', NULL, 1, '2025-11-10 10:24:07', '2025-12-23 06:22:07'),
(6, 'Student', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'exams.attempt\',\'exams.register\',\'questions.view\',\'results.my\',\'results.review\',\'profile.view\',\'profile.edit\',\'profile.change_password\',\'exams.my\']', NULL, 1, '2025-11-10 10:24:07', '2025-12-23 05:26:38'),
(7, 'Parent', '[\'dashboard.view\',\'dashboard.reports\',\'dashboard.analytics\',\'dashboard.notifications\',\'questions.view\',\'results.my\',\'results.review\',\'profile.view\',\'profile.change_password\',\'profile.edit\',\'exams.my\']', NULL, 1, '2025-11-10 10:24:07', '2025-12-23 05:26:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_info`
--
ALTER TABLE `exam_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_registration`
--
ALTER TABLE `exam_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`exam_id`,`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `exam_settings`
--
ALTER TABLE `exam_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exam_info`
--
ALTER TABLE `exam_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `exam_registration`
--
ALTER TABLE `exam_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exam_settings`
--
ALTER TABLE `exam_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registration` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_ibfk_3` FOREIGN KEY (`exam_id`) REFERENCES `exam_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_registration`
--
ALTER TABLE `exam_registration`
  ADD CONSTRAINT `exam_registration_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_registration_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
