-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 02, 2026 at 12:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biblioteka`
--

-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS biblioteka;
USE biblioteka;
--
-- Table structure for table `audit_zurnals`
--

CREATE TABLE `audit_zurnals` (
  `id` int NOT NULL,
  `darbinieks` varchar(40) NOT NULL,
  `darbiba` enum('pievienots','redigēts','dzēsts','ielogošanās','izlogošanās','mainīts statuss','meklēts') NOT NULL,
  `tabula` varchar(50) NOT NULL,
  `ieraksta_nosaukums` varchar(150) NOT NULL,
  `laiks` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_zurnals`
--

INSERT INTO `audit_zurnals` (`id`, `darbinieks`, `darbiba`, `tabula`, `ieraksta_nosaukums`, `laiks`) VALUES
(1, 'admin', 'ielogošanās', '-', '-', '2026-09-27 14:58:01'),
(2, 'admin', 'ielogošanās', '-', '-', '2026-09-28 13:50:26'),
(3, 'admin', 'izlogošanās', '-', '-', '2026-09-28 15:00:31'),
(4, 'admin', 'ielogošanās', '-', '-', '2026-09-28 15:00:33'),
(5, 'admin', 'redigēts', 'Lasītāji', 'Lasītājs #1:\n Uzvārds: Cābele -> Cābel', '2026-09-28 15:41:14'),
(6, 'admin', 'redigēts', 'Lasītāji', 'Lasītājs #1:\n Uzvārds: Cābel -> Cābele', '2026-09-28 15:44:10'),
(7, 'admin', 'dzēsts', 'Lasītāji', 'tests (#3)', '2026-09-28 15:54:28'),
(8, 'admin', 'meklēts', 'Lasītāji', 'baiba', '2026-09-28 16:09:14'),
(9, 'admin', 'meklēts', 'Lasītāji', 'test', '2026-09-28 16:09:19'),
(10, 'admin', 'meklēts', 'Lasītāji', 'baiba', '2026-09-28 16:09:22'),
(11, 'admin', 'meklēts', 'Lasītāji', 'baiba', '2026-09-28 16:09:24'),
(12, 'admin', 'meklēts', 'Lasītāji', 'andri', '2026-09-28 16:09:27'),
(13, 'admin', 'meklēts', 'Lasītāji', 'baiba', '2026-09-28 16:09:31'),
(14, 'admin', 'izlogošanās', '-', '-', '2026-09-28 18:08:41'),
(15, 'admin', 'ielogošanās', '-', '-', '2026-09-28 18:09:38'),
(16, 'admin', 'izlogošanās', '-', '-', '2026-09-28 18:09:41'),
(17, 'kristovskis', 'ielogošanās', '-', '-', '2026-09-28 18:09:46'),
(18, 'kristovskis', 'izlogošanās', '-', '-', '2026-09-28 18:09:54'),
(19, 'admin', 'ielogošanās', '-', '-', '2026-09-28 18:09:58'),
(20, 'admin', 'ielogošanās', '-', '-', '2026-09-29 11:31:33'),
(21, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #15:\n Autors: Ryunosuke Akutagawa -> Ryunosuke Akutaga', '2026-09-29 12:35:54'),
(22, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #15:\n Autors: Ryunosuke Akutaga -> Ryunosuke Akutagawa,\n Bilde: Mainīta', '2026-09-29 12:36:19'),
(23, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #15:\n Bilde: Mainīta', '2026-09-29 12:36:54'),
(24, 'admin', 'pievienots', 'Grāmatas', 'tests', '2026-09-29 12:38:11'),
(25, 'admin', 'dzēsts', 'Grāmatas', 'tests', '2026-09-29 12:39:49'),
(26, 'admin', 'redigēts', 'Lasītāji', 'Lasītājs #4:\n Epasts: janis.berzs@gmail.com -> janis.berzs@gmail.co', '2026-09-29 13:16:30'),
(27, 'admin', 'redigēts', 'Lasītāji', 'Lasītājs #4:\n Epasts: janis.berzs@gmail.co -> janis.berzs@gmail.com', '2026-09-29 13:16:34'),
(28, 'admin', 'meklēts', 'Lasītāji', 'baiba', '2026-09-29 13:33:58'),
(29, 'admin', 'meklēts', 'Lasītāji', 'janis', '2026-09-29 13:34:01'),
(30, 'admin', 'meklēts', 'Grāmatas', 'all', '2026-09-29 13:55:28'),
(31, 'admin', 'meklēts', 'Grāmatas', 'all', '2026-09-29 14:17:07'),
(32, 'admin', 'meklēts', 'Grāmatas', 'in', '2026-09-29 14:17:10'),
(33, 'admin', 'meklēts', 'Grāmatas', 'harijs', '2026-09-29 14:17:23'),
(34, 'admin', 'meklēts', 'Grāmatas', 'murakami', '2026-09-29 14:17:28'),
(35, 'admin', 'meklēts', 'Grāmatas', 'dosto', '2026-09-29 14:17:31'),
(36, 'admin', 'meklēts', 'Grāmatas', 'tumsai', '2026-09-29 14:17:41'),
(37, 'admin', 'ielogošanās', '-', '-', '2026-09-30 16:01:33'),
(38, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #15:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:19'),
(39, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #14:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:23'),
(40, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #13:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:27'),
(41, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #9:\n Statuss: paņemts -> pieejams', '2026-09-30 17:47:41'),
(42, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #7:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:46'),
(43, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #5:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:52'),
(44, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #4:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:47:55'),
(45, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #1:\n Statuss: rezervēts -> pieejams', '2026-09-30 17:48:02'),
(46, 'admin', 'meklēts', 'Grāmatas', 'alises', '2026-09-30 18:21:43'),
(47, 'admin', 'meklēts', 'Grāmatas', 'harijs', '2026-09-30 18:22:01'),
(48, 'admin', 'meklēts', 'Grāmatas', 'ha', '2026-09-30 18:22:04'),
(49, 'admin', 'izlogošanās', '-', '-', '2026-09-30 19:05:33'),
(50, 'admin', 'ielogošanās', '-', '-', '2026-09-30 19:08:02'),
(51, 'admin', 'izlogošanās', '-', '-', '2026-09-30 23:34:19'),
(52, 'admin', 'ielogošanās', '-', '-', '2026-09-30 23:34:22'),
(53, 'admin', 'mainīts statuss', 'Rezervācijas', 'Pasūtījums #4 (aktīva)', '2026-09-30 23:37:23'),
(54, 'admin', 'mainīts statuss', 'Rezervācijas', 'Pasūtījums #4 (pabeigta)', '2026-09-30 23:38:40'),
(55, 'admin', 'mainīts statuss', 'Rezervācijas', 'Pasūtījums #1 (aktīva)', '2026-09-30 23:38:41'),
(56, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #1 (pabeigta)', '2026-09-30 23:50:57'),
(57, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #3 (atcelta)', '2026-09-30 23:51:17'),
(58, 'admin', 'meklēts', 'Grāmatas', 'norwegian', '2026-09-30 23:51:26'),
(59, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #11:\n Statuss: rezervēts -> pieejams', '2026-10-01 00:04:34'),
(60, 'admin', 'meklēts', 'Grāmatas', 'norwegian', '2026-10-01 00:04:34'),
(61, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (aktīva)', '2026-10-01 00:05:35'),
(62, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (gaida)', '2026-10-01 00:05:39'),
(63, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (aktīva)', '2026-10-01 00:05:42'),
(64, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (gaida)', '2026-10-01 00:06:57'),
(65, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (aktīva)', '2026-10-01 00:07:05'),
(66, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (pabeigta)', '2026-10-01 00:07:41'),
(67, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #5 (pabeigta)', '2026-10-01 00:08:21'),
(68, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #6 (atcelta)', '2026-10-01 00:11:03'),
(69, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #7 (atcelta)', '2026-10-01 00:12:39'),
(70, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #7 (atcelta)', '2026-10-01 00:13:43'),
(71, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #8 (atcelta)', '2026-10-01 00:26:44'),
(72, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #9 (aktīva)', '2026-10-01 00:27:04'),
(73, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #9 (pabeigta)', '2026-10-01 00:27:05'),
(74, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #14:\n Statuss: rezervēts -> pieejams', '2026-10-01 00:27:18'),
(75, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #13:\n Statuss: rezervēts -> pieejams', '2026-10-01 00:27:30'),
(76, 'admin', 'meklēts', 'Grāmatas', 'harijs', '2026-10-01 00:27:35'),
(77, 'admin', 'redigēts', 'Grāmatas', 'Grāmata #10:\n Statuss: rezervēts -> pieejams', '2026-10-01 00:27:40'),
(78, 'admin', 'meklēts', 'Grāmatas', 'harijs', '2026-10-01 00:27:40'),
(79, 'admin', 'ielogošanās', '-', '-', '2026-10-01 12:08:44'),
(80, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #10 (atcelta)', '2026-10-01 13:08:27'),
(81, 'admin', 'dzēsts', 'Lasītāji', 'Daiga (#5)', '2026-10-01 13:22:49'),
(82, 'admin', 'dzēsts', 'Lasītāji', 'Baiba (#1)', '2026-10-01 13:23:47'),
(83, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #6 (atcelta)', '2026-10-01 13:53:26'),
(84, 'admin', 'dzēsts', 'Lasītāji', 'Baiba (#6)', '2026-10-01 13:53:26'),
(85, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #11 (atcelta)', '2026-10-01 13:53:44'),
(86, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #12 (atcelta)', '2026-10-01 13:53:44'),
(87, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #14 (atcelta)', '2026-10-01 16:24:26'),
(88, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #15 (atcelta)', '2026-10-01 16:24:27'),
(89, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #16 (atcelta)', '2026-10-01 16:24:27'),
(90, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #17 (atcelta)', '2026-10-01 16:58:06'),
(91, 'admin', 'redigēts', 'Lasītāji', 'Lasītājs #8:\n Epasts: test@test.lv -> test@test.l', '2026-10-01 17:48:30'),
(92, 'admin', 'pievienots', 'Lasītāji', 'Daiga Krūmiņa', '2026-10-01 17:50:05'),
(93, 'admin', 'dzēsts', 'Lasītāji', 'Jānis (#8)', '2026-10-01 17:50:17'),
(94, 'admin', 'pievienots', 'Darbinieki', 'tests (moders)', '2026-10-01 18:58:24'),
(95, 'admin', 'redigēts', 'Darbinieki', 'Darbinieks #4:\n Parole nomainīta', '2026-10-01 18:58:40'),
(96, 'admin', 'izlogošanās', '-', '-', '2026-10-01 18:58:42'),
(97, 'tests', 'ielogošanās', '-', '-', '2026-10-01 18:58:50'),
(98, 'tests', 'izlogošanās', '-', '-', '2026-10-01 18:58:55'),
(99, 'admin', 'ielogošanās', '-', '-', '2026-10-01 18:59:02'),
(100, 'admin', 'meklēts', 'Darbinieki', 'test', '2026-10-01 18:59:09'),
(101, 'admin', 'meklēts', 'Darbinieki', 'moder', '2026-10-01 18:59:12'),
(102, 'admin', 'meklēts', 'Darbinieki', 'moders', '2026-10-01 18:59:14'),
(103, 'admin', 'dzēsts', 'Darbinieki', 'tests', '2026-10-01 18:59:18'),
(104, 'admin', 'pievienots', 'Lasītāji', 'tests tests', '2026-10-01 19:28:09'),
(105, 'admin', 'dzēsts', 'Lasītāji', 'tests (#10)', '2026-10-01 19:28:36'),
(106, 'admin', 'ielogošanās', '-', '-', '2026-10-02 09:39:55'),
(107, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #21 (aktīva)', '2026-10-02 09:40:43'),
(108, 'admin', 'izlogošanās', '-', '-', '2026-10-02 09:43:28'),
(109, 'admin', 'ielogošanās', '-', '-', '2026-10-02 09:43:42'),
(110, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #22 (atcelta)', '2026-10-02 09:51:58'),
(111, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #23 (atcelta)', '2026-10-02 09:51:59'),
(112, 'admin', 'izlogošanās', '-', '-', '2026-10-02 10:27:31'),
(113, 'admin', 'ielogošanās', '-', '-', '2026-10-02 10:27:35'),
(114, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #21 (pabeigta)', '2026-10-02 10:37:16'),
(115, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #20 (aktīva)', '2026-10-02 10:37:17'),
(116, 'admin', 'izlogošanās', '-', '-', '2026-10-02 11:10:31'),
(117, 'admin', 'ielogošanās', '-', '-', '2026-10-02 11:10:33'),
(118, 'admin', 'meklēts', 'Žurnālfaili', 'ielogošanās', '2026-10-02 11:13:35'),
(119, 'admin', 'izlogošanās', '-', '-', '2026-10-02 11:13:47'),
(120, 'moder', 'ielogošanās', '-', '-', '2026-10-02 11:13:51'),
(121, 'moder', 'izlogošanās', '-', '-', '2026-10-02 11:15:35'),
(122, 'admin', 'ielogošanās', '-', '-', '2026-10-02 11:15:39'),
(123, 'admin', 'izlogošanās', '-', '-', '2026-10-02 11:25:10'),
(124, 'moder', 'ielogošanās', '-', '-', '2026-10-02 11:25:14'),
(125, 'moder', 'izlogošanās', '-', '-', '2026-10-02 11:25:18'),
(126, 'admin', 'ielogošanās', '-', '-', '2026-10-02 11:25:22'),
(127, 'admin', 'izlogošanās', '-', '-', '2026-10-02 11:56:12'),
(128, 'admin', 'ielogošanās', '-', '-', '2026-10-02 11:56:15'),
(129, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #25 (aktīva)', '2026-10-02 12:14:21'),
(130, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #25 (pabeigta)', '2026-10-02 12:14:22'),
(131, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #27 (aktīva)', '2026-10-02 12:14:23'),
(132, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #27 (pabeigta)', '2026-10-02 12:14:23'),
(133, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #26 (atcelta)', '2026-10-02 12:14:24'),
(134, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #34 (aktīva)', '2026-10-02 12:18:25'),
(135, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #34 (pabeigta)', '2026-10-02 12:18:25'),
(136, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #19 (aktīva)', '2026-10-02 12:18:27'),
(137, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #19 (pabeigta)', '2026-10-02 12:18:29'),
(138, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #30 (atcelta)', '2026-10-02 12:18:38'),
(139, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #33 (atcelta)', '2026-10-02 12:18:39'),
(140, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #29 (atcelta)', '2026-10-02 12:18:43'),
(141, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #28 (atcelta)', '2026-10-02 12:18:43'),
(142, 'admin', 'meklēts', 'Lasītāji', 'krums', '2026-10-02 12:28:13'),
(143, 'admin', 'meklēts', 'Lasītāji', 'A', '2026-10-02 12:28:17'),
(144, 'admin', 'meklēts', 'Lasītāji', 'A', '2026-10-02 12:28:19'),
(145, 'admin', 'meklēts', 'Lasītāji', 'A', '2026-10-02 12:28:20'),
(146, 'admin', 'meklēts', 'Lasītāji', 'gmail', '2026-10-02 12:28:22'),
(147, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #24 (aktīva)', '2026-10-02 12:39:16'),
(148, 'admin', 'mainīts statuss', 'Rezervācijas', 'Rezervācija #31 (aktīva)', '2026-10-02 12:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `darbinieki`
--

CREATE TABLE `darbinieki` (
  `id` int NOT NULL,
  `lietotajvards` varchar(40) NOT NULL,
  `parole` varchar(60) NOT NULL,
  `loma` enum('administrators','moders') NOT NULL DEFAULT 'moders',
  `statuss` enum('Aktīvs','Dzēsts') NOT NULL DEFAULT 'Aktīvs'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `darbinieki`
--

INSERT INTO `darbinieki` (`id`, `lietotajvards`, `parole`, `loma`, `statuss`) VALUES
(1, 'admin', '$2y$10$kFlUyaN3If4DtxpTci4vRO9hvIQJGbPksDYBypqY44J9T/CqsoFoe', 'administrators', 'Aktīvs'),
(2, 'moder', '$2y$10$w7Zm06.LoFxO57yOtCVmBOSvdXAtkxoo8uolFm4q2iPYvQPj0wffG', 'moders', 'Aktīvs'),
(3, 'kristovskis', '$2y$10$fKHtxH2tUB//AN7W9SS1Ce/phsROSW8Vf0XYnCCEsiJ596iTV9sDC', 'administrators', 'Aktīvs'),
(4, 'tests', '$2y$10$rCaAHVuyuh6qrpzLZI7s0uWowTB7JO.zCv2NTq8yt18Wlbrh15fdO', 'moders', 'Dzēsts'),
(5, 'andis', '$2y$10$/AKvOXw.wC86SPEOGHPmi.9hofu7Kn1NDTeFiQ5q7VUUX7kZOVU16', 'moders', 'Aktīvs'),
(6, 'karlis', '$2y$10$v4U2MLgApfHEVXNhumInKOqb82d55NvsKAkVxpTcQ8YRN62/oXnAu', 'moders', 'Aktīvs'),
(7, 'cabele', '$2y$10$QhaVqqobMF81XAdRBDa20uzvWJaDVdaLpV9A4Nb8n5HFJFR/eN4hq', 'administrators', 'Aktīvs'),
(8, 'administrators2', '$2y$10$GOI3cgQBiKgdsAMxTjZFoufwULIzB8XOzu9oWhYPLnd91HFkL1G4q', 'administrators', 'Aktīvs'),
(9, 'janis.b', '$2y$10$Ft5c9cvvzdnhyyXftgQNb.upFbKme6pq9uxwdkCnjdy0wkfxv2ASy', 'administrators', 'Aktīvs'),
(10, 'klava', '$2y$10$muRKfuO5wXVryFglWKdMfeqSVG9/b1H0uY1LxwzF5x1fGDHVKBQQe', 'moders', 'Aktīvs'),
(11, 'andrejs', '$2y$10$LCByodVrtHhIzsYzSYaW8eICW8diRzvTVlc2dbxtj3CYJ7HnsiA2a', 'moders', 'Aktīvs'),
(12, 'admin2', '$2y$10$pdncGB.Dq4IldislpDdbCONsBgLQivA2qneCMERwiUctf5XffwRn6', 'moders', 'Aktīvs'),
(13, 'liepa', '$2y$10$EzS5B1HKRlT2vxFjVcX6F.XzKOUBoI0jSt9FNgyeGoaEK8MZu78N.', 'moders', 'Aktīvs'),
(14, 'marta.p', '$2y$10$NfkUIbxfit64bB2lpc2YnuD1bKal5VouRchIx5DuGyc4o9VlBJE/K', 'administrators', 'Aktīvs'),
(15, 'kristofers', '$2y$10$bqlNwcRJHjNG.IqEP0WeGebvQ148vJWpGU00SkgXf.hyuoILih/oG', 'moders', 'Aktīvs'),
(16, 'anna.l', '$2y$10$paps06qnxZeUImzCN4eJ7OI6cUdvMglaBNj6v2dY.lm9Tun7pwQza', 'moders', 'Aktīvs');

-- --------------------------------------------------------

--
-- Table structure for table `gramatas`
--

CREATE TABLE `gramatas` (
  `id` int NOT NULL,
  `nosaukums` varchar(80) NOT NULL,
  `autors` varchar(80) NOT NULL,
  `apraksts` text NOT NULL,
  `statuss` enum('pieejams','rezervēts','paņemts','kavē','dzēsts') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pieejams',
  `bilde` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gramatas`
--

INSERT INTO `gramatas` (`id`, `nosaukums`, `autors`, `apraksts`, `statuss`, `bilde`) VALUES
(1, 'Alises piedzīvojumi Brīnumzemē', 'Lūiss Kerols', 'Reiz meitene, vārdā Alise, ieraudzījusi runājošu Balto Trusi, daudz nedomādama, metās tam pakaļ alā un nonāca Brīnumzemē. Šajā fantāzijas pasaulē viņa sastapa dažādas jocīgas būtnes un piedzīvoja daudz neparasta: jūras vēži dejoja kadriļu, atdzīvojās spēļu kārtis, Viltotais Bruņurupucis stāstīja par jūras skolu, Zilais Kāpurs sūca ūdenspīpi, bet Češīras Kaķis viltīgi smīnēja. Grāmatā aprakstītas dažādas burvestības un brīnumi – tieši to mums reizēm tik ļoti pietrūkst ikdienā.\r\n\r\n“Alises piedzīvojumi Brīnumzemē” ir lieliska lasāmviela ne vien bērniem, bet arī pieaugušajiem. Smieklīgie varoņi, jautrās situācijas, aizraujošie dialogi un brīnišķīgās ilustrācijas, kuras valdzina ar savu lirisko noskaņu, piešķir šai grāmatai īpašu spilgtumu un pievilcību.\r\n\r\nTulkojusi Elfrīda Melbārzde. ', 'pieejams', 'alises-piedzivojumi-brinumzeme.jpg'),
(2, 'Vinnijs Pūks un viņa draugi', 'Alans Aleksandrs Milns', 'Mežs, kurā mājo labsirdīgais lācītis Vinnijs Pūks, ir visvisādu pārsteigumu, piedzīvojumu un pārpratumu pilns. Pūks un viņa draugi nokļūst dažādās neticamās ķibelēs, sākot ar kāpšanu bišu kokā, ekspotīciju uz Ziemeļpolu un beidzot ar Pucmājas atrašanu.\r\nLīksmi un skumīgi, čakli un laiski, staigājoši, rāpojoši un lidojoši, bet vienmēr asprātīgi – tādi ir šī darba tēli.\r\nŠajā izdevumā apvienotas divas grāmatas: „Vinnijs Pūks” un „Jaunā māja Pūka Priedītēs”.', 'pieejams', 'vinnijs-puks-un-vina-draugi.jpg'),
(3, 'Brāļi Lauvassirdis', 'Astrida Lindgrēne', 'Sirdi plosošs stāsts par diviem brāļiem, no kuriem viens mirst, bet abi viņi ir sarunājuši satikties izsapņotajā Nangijālā, kurā viss ir iespējams.', 'pieejams', 'brali-lauvassirdis.jpg'),
(4, 'Emīls un Berlīnes zēni', 'Ērihs Kestners', 'Pirmo reizi Emīls drīkst viens braukt uz Berlīni. Vecmāmiņa un māsīca Ponija Cepurīte gaida viņu Frīdriha ielas stacijā. Bet Emīls neierodas, arī ar nākamo vilcienu ne. Kamēr vecmāmiņa un Ponija Cepurīte domā, ko darīt, Emīls jau ir ierauts notikumu virpulī. Lielajā un svešajā pilsētā Emīls seko vīram, kurš viņu vilcienā apzadzis. Par laimi, Emīlam palīdz Gustavs ar autotauri un viņa draugi.', 'rezervēts', 'emils-un-berlines-zeni.jpg'),
(5, 'Divas Lotiņas', 'Ērihs Kestners', 'Divas meitenes, kuras agrāk neko viena par otru nav zinājušas, pēkšņi satiekas atpūtas nometnē un atskārš, ka ir viena otrai tik līdzīgas kā divas ūdens lāses. Lai atklātu noslēpumu, viņas samainās – Luīze dodas uz Minheni Lotes vietā, bet Lote – uz Vīni Luīzes vietā…', 'pieejams', 'divas-lotinas.jpg'),
(6, 'Punktiņa un Antons', 'Ērihs Kestners', 'Nabadzīgajam zēnam Antonam varētu klāties daudz grūtāk, ja viņam nebūtu tik uzticama draudzene kā direktora Poges kunga meita draiskā un izdomas bagātā Punktiņa.\r\n\r\nNo zviedru valodas tulkojusi Zigrīda Plaka.', 'pieejams', 'punktina-un-antons.jpg'),
(7, 'Lesi atgriežas mājās', 'Eriks Naits', 'Romāna darbība norisinās Anglijā 20. gadsimta 30. gados, pasaules ekonomiskās krīzes laikā.\r\n\r\nDivpadsmitgadīgā Džo uzticamā pavadone Lesija ir godalgots suns. Taču, kad Džo tēvs zaudē darbu, ģimenei nākas Lesiju pārdot. Trīs reizes tā aizbēg no sava jaunā īpašnieka un pārnāk pie Džo, līdz tiek aizvesta uz nomaļu Skotijas reģionu - pārāk tālu, lai kāds suns spētu atgriezties Jorkšīrā vien pats saviem spēkiem. Bet Lesija nav kurš katrs suns... Lai atgrieztos pie sava saimnieka, drošsirdīgajai Lesijai jāpārvar daudz bīstamu šķēršļu gandrīz tūkstoš jūdžu garā ceļā. Taču viņu nespēj apturēt nekas...\r\n\r\nNo angļu valodas tulkojusi Ilze Gintere', 'pieejams', 'lesija-atgriezas-majas.jpg'),
(8, 'Ronja - laupītāja meita', 'Astrida Lindgrēne', 'Grāmata par to, kā raupjajā laupītāju vidē bērni ir tie, kas ar savu rīcību mudina pieaugušos saprast, ka īsta draudzība, mīlestība un savstarpējā izpalīdzība ir lielāks spēks nekā naids un savstarpējā konkurence.', 'dzēsts', 'ronja-laupitaja-meita.jpg'),
(9, 'Harijs Poters un filozofu akmens, 1', 'Džoanna K. Roulinga', 'HARIJS POTERS neko nebija dzirdējis par Cūkkārpu... bet tad Dzīvžogu ielas ceturtā nama pastkastītē sāka birt VĒSTULES.\r\nRakstītas uz dzeltenīga pergamenta ar ZAĻU TINTI un rotātas ar PURPURKRĀSAS ZĪMOGU.\r\nHarija nejaukā tante un tēvocis žigli vien šīs vēstules PIEVĀC.\r\nTaču Harija vienpadsmitajā dzimšanas dienā ierodas MILZIS, vārdā RUBEUSS HAGRIDS, un pavēsta NETICAMU vēsti:\r\nHarijs Poters patiesībā ir burvis,\r\nun viņam jādodas mācīties uz CŪKKĀRPAS RAGANĪBAS UN BURVESTĪBU ARODSKOLU.\r\nUn tas ir tikai neparasto piedzīvojumu sākums...\r\n\r\nNo angļu valodas tulkojis Ingus Josts.\r\n\r\nDŽ. K. ROULINGA ir autore satriecoši populārajai, daudzkārt apbalvotajai grāmatu sērijai par Hariju Poteru. Šos romānus dievina visas pasaules lasītāji, tie izdoti vairāk nekā 500 miljonos eksemplāru, tulkoti 80 valodās, un pēc to motīviem tapušas astoņas pilnmetrāžas filmas. Lai atbalstītu labdarības mērķus, autore sērijai sarakstījusi vēl trīs papildinājumus: \"Kalambols laiku lokos\", \"Fantastiskās būtnes un kur tās meklēt\" (atbalstot Comic Aid un Lumos) un \"Dziesminieka Ķestera pasakas\" (atbalstot Lumos), kā arī grāmatas \"Fantastiskās būtnes un kur tās meklēt\" iedvesmotu scenāriju, kas aizsāka topošu piecu filmu sēriju. Rakstniece arī piedalījās lugas \"Harijs Poters un Nolādētais bērns - 1. un 2. daļa\" tapšanā, kuru iestudēja 2016. gada vasarā Londonā, Vestendā un 2018 gada sākumā - Brodvejā. 2012. gadā tika dibināts Dž. K. Roulingas digitālais uzņēmums Pottermore - vietne, kurā grāmatu fani var lasīt jaunākās ziņas un rakstus, kā arī jaunākos autores radītos tekstus. Dž. K. Roulinga saņēmusi daudzus apbalvojumus un goda nosaukumus, to skaitā Hansa Kristiana Andersena balvu, Francijas Goda leģiona ordeni un ordeņa Companions of Honour biedres statusu.', 'pieejams', 'harijs-poters.jpg'),
(10, 'Harijs Poters un Azkabanas gūsteknis, 3', 'Džoanna K. Roulinga', 'Kad cauri tumsai izbrāžas KNAKTS AUTOBUSS un NOBREMZĒ Harija Potera priekšā, tas nozīmē tikai vienu - ir sācies jauns CŪKKĀRPAS mācību gads, kurā netrūks piedzīvojumu. No cietuma ir IZBĒDZIS masu slepkava un LORDA VOLDEMORTA atbalstītājs Siriuss Bleks - un klīst baumas, ka viņš uzglūn tieši Harijam. Savā pirmajā Pareģošanas nodarbībā profesore Trilonija Harija tējas biezumos saskata NĀVES ZĪMI... Taču ŠAUSMINOŠĀKI par visu ir baisie ATPRĀTOTĀJI, kas apsargā skolas teritoriju, un viņu SKŪPSTS, kas izsūc upura dvēseli...\r\n\r\nNo angļu valodas tulkojis Ingus Josts.\r\n\r\nDŽ. K. ROULINGA visā pasaulē ir pazīstama kā autore septiņām grāmatām par Hariju Poteru, kuras tika izdotas laikā no 1997. līdz 2007. gadam. Visiem zināms slavenais stāsts par to, kā šo grāmatu ideja autorei ienāca prātā, braucot vilcienā no Mančestras uz Londonu. Joprojām populārie Harija un viņa draugu Rona un Hermiones piedzīvojumu stāsti ir izdoti vairāk nekā 500 miljonos eksemplāru, tulkoti 80 valodās, un pēc to motīviem tapušas astoņas pilnmetrāžas filmas. Lai atbalstītu labdarības mērķus, autore sērijai sarakstījusi vēl trīs papildinājumus: “Kalambols laiku lokos”, “Fantastiskās būtnes un kur tās meklēt” (atbalstot Comic Relief un Lumos) un “Dziesminieka Ķestera pasakas” (atbalstot Lumos). Sadarbībā ar dramaturgu Džeku Tornu (Jack Thorne) un režisoru Džonu Tifaniju (John Tiffany) Dž. K. Roulinga piedalījās lugas “Harijs Poters un Nolādētais bērns” tapšanā, kuru iestudēja 2016. gadā Londonā un šobrīd izrāda Eiropā, Ziemeļamerikā un Austrālijā. Šai pašā gadā rakstniece debitēja arī kā scenāriste, radot grāmatas “Fantastiskās būtnes un kur tās meklēt” iedvesmotu scenāriju filmai – pirmajai topošajā sērijā par maģiskās zooloģijas eksperta Tritona Skamandera piedzīvojumiem.\r\nLaikā, kad Dž. K. Roulinga neraksta par burvju pasauli, viņa rada romānus pieaugušajiem, kuru darbība notiek reālajā pasaulē. Cik vien autore spēj sevi atcerēties, viņa vienmēr ir vēlējusies kļūt par rakstnieci, un vislaimīgākā jūtas vienatnē, kad var izdomāt jaunus stāstus. Dž. K. Roulinga saņēmusi daudzus apbalvojumus un goda nosaukumus, to skaitā Britu impērijas ordeni, ordeņa Companions of Honour biedres statusu un BBC bērnu televīzijas balvu Blue Peter Gold Badge. Rakstniece kopā ar ģimeni dzīvo Skotijā.', 'rezervēts', 'harijs-poters-3.jpg'),
(11, 'Norwegian Wood', 'Haruki Murakami', 'Tokijas koledžas students Toru, klusais un ārkārtīgi nopietnais jaunietis, ir uzticīgs Naoko, skaistai un introspektīvai jaunai sievietei, taču viņu savstarpējo kaislību iezīmē traģiskā labākā drauga nāve pirms vairākiem gadiem. Toru sāk pielāgoties universitātes pilsētiņas dzīvei un vientulībai un izolētībai, ar ko viņš tur saskaras, bet Naoko uzskata, ka dzīves spiediens un pienākumi ir nepanesami. Kamēr viņa aizvien vairāk noslēdzas savā pasaulē, Toru pieķer sevi, tiecoties pēc citiem un pievelkoties pie ārkārtīgi neatkarīgas un seksuāli atbrīvotas jaunas sievietes.\r\n\r\nLieliskā sešdesmito gadu mūzikas, noskaņas un ētikas apvienojumā ar stāstu par viena koledžas studenta romantisko nobriešanu \"Norwegian Wood\" izcili ataino jauna cilvēka pirmo, bezcerīgo un varonīgo mīlestību.', 'pieejams', 'norwegian-wood.jpg'),
(12, 'Crime and Punishment', 'Fjodors Dostojevskis', 'Ievērojamā krievu rakstnieka Fjodora Dostojevska (1821–1881) filozofiski psiholoģiskais romāns “Noziegums un sods” (1866) ir viens no pasaulē pazīstamākajiem krievu literatūras darbiem.Rakstnieks tajā atklāj nozieguma sociālos un psiholoģiskos cēloņus, kā arī noziedznieka izjūtas.\r\nRomāna sižeta pavērsieni ir baisi, neparedzami un tumši kā neskaitāmās šķērsielas, kāpnes un gaiteņi, pa kuriem rakstnieks izvadā lasītāju. Viņa radīto personāžu dvēseles bezdibeņi – dziļi un drūmi kā pagalmu akas.\r\n\r\nGalvenais varonis – nabadzīgs students Rodions Raskoļņikovs –, aizrāvies ar pārcilvēka ideju, nogalina augļotāju, uzskatot, ka viņa citiem nes tikai sāpes un ciešanas. Taču, nemitīgi cenšoties pārliecināt sevi, ka tikai iznīcinājis cilvēku, kam nav tiesību dzīvot, un atbrīvojis citus no tā, viņš aizvien vairāk ieraujas sevī un patiesībā soda pats sevi.', 'rezervēts', 'crime-and-punishment.jpg'),
(13, 'No Longer Human', 'Osamu Dazai', 'Nav', 'rezervēts', 'no-longer-human.jpg'),
(14, 'All The Lovers In The Night', 'Mieko Kawakami', 'Šis liriskais, bieži vien šokējošais romāns, ko sarakstījusi \"Breasts and Eggs\" autore un kurā izmantotas žilbinoši realizētas gaismas un emocionālā izgaismojuma tēmas, stāsta par 30 gadus vecu korektori un fizikas skolotāju, kurš viņai piedāvā iespēju izglābties.', 'pieejams', 'all-the-lovers-in-the-night.jpg'),
(15, 'Hell Screen (Little Clothbound Classics)', 'Ryunosuke Akutagawa', 'Iepazīstinām ar “Little Clothbound Classics” sēriju: neatvairāmiem pasaules izcilāko rakstnieku īso stāstu, noveļu un eseju miniatūriem izdevumiem, ko veidojusi godalgotā Koralī Bikforda-Smita (Coralie Bickford-Smith).\r\n\r\nPriecājoties par Penguin Classics grāmatu klāstu un daudzveidību, tie mūs aizvedīs no sniegotās Japānas līdz pavasarīgajai Vīnei, no Ņūanglijas, kur valda spoki, līdz saules pielietai Vidusjūras salai un no šaha spēles okeānā līdz mīlas stāstam uz Mēness. Šie kolekcionējamie izdevumi ir skaisti noformēti un iesieti krāsainā, taustes ziņā patīkamā audumā un apdrukāti ar foliju.\r\n', 'rezervēts', '6abbb0e68fc5f_hell-screen.jpg'),
(16, 'tests', 'trest', 'test', 'dzēsts', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `lasitaji`
--

CREATE TABLE `lasitaji` (
  `id` int NOT NULL,
  `vards` varchar(60) NOT NULL,
  `uzvards` varchar(60) NOT NULL,
  `epasts` varchar(80) NOT NULL,
  `statuss` enum('Aktīvs','Dzēsts') NOT NULL DEFAULT 'Aktīvs'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lasitaji`
--

INSERT INTO `lasitaji` (`id`, `vards`, `uzvards`, `epasts`, `statuss`) VALUES
(1, 'Baiba', 'Cābele', 'cabelebaiba@gmail.com', 'Dzēsts'),
(2, 'Jānis', 'Bērziņs', 'janisb@inbox.lv', 'Dzēsts'),
(3, 'tests', 'tests', 'test@test.com', 'Dzēsts'),
(4, 'Jānis', 'Bērzs', 'janis.berzs@gmail.com', 'Aktīvs'),
(5, 'Daiga', 'Lapsa', 'd.lapsa@tests.com', 'Dzēsts'),
(6, 'Baiba', 'Cābele', 'cabelebaiba@gmail.com', 'Dzēsts'),
(7, 'Baiba', 'Cābele', 'cabelebaiba@gmail.com', 'Aktīvs'),
(8, 'Jānis', 'Bērzs', 'test@test.l', 'Dzēsts'),
(9, 'Daiga', 'Krūmiņa', 'd.krumina@gmail.com', 'Aktīvs'),
(10, 'tests', 'tests', 'cabelebaiba@gmail.com', 'Dzēsts'),
(11, 'Jānis', 'Kļava', 'j.klava@gmail.com', 'Aktīvs'),
(12, 'Daiga', 'Krūmiņa', 'd.krumina@inbox.lv', 'Aktīvs'),
(13, 'Marta', 'Kaija', 'kaijamar@gmail.com', 'Aktīvs'),
(14, 'Kārlis', 'Lapa', 'k.lapa@gmail.com', 'Aktīvs'),
(15, 'Karlīna', 'Jansone', 'k.jansone@inbox.lv', 'Aktīvs'),
(16, 'Jānis', 'Lapsa', 'lapsaj@inbox.lv', 'Aktīvs'),
(17, 'Alberts', 'Kālis', 'kal.alb@gmail.com', 'Aktīvs'),
(18, 'Līga', 'Lasmane', 'l.lasmane@inbox.lv', 'Aktīvs'),
(19, 'Karlīna', 'Lapsa', 'karlina@gmail.com', 'Aktīvs'),
(20, 'Arnis', 'Lasmanis', 'a.lasmanis@inbox.lv', 'Aktīvs'),
(21, 'Sandra', 'Lasmane', 'sandra@gmail.com', 'Aktīvs'),
(22, 'Aleks', 'Krūms', 'krums@inbox.lv', 'Aktīvs');

-- --------------------------------------------------------

--
-- Table structure for table `rezervacijas`
--

CREATE TABLE `rezervacijas` (
  `id` int NOT NULL,
  `gramata_id` int NOT NULL,
  `lasitajs_id` int NOT NULL,
  `rezervacijas_datums` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atdosanas_datums` timestamp NULL DEFAULT NULL,
  `statuss` enum('gaida','aktīva','pabeigta','atcelta') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rezervacijas`
--

INSERT INTO `rezervacijas` (`id`, `gramata_id`, `lasitajs_id`, `rezervacijas_datums`, `atdosanas_datums`, `statuss`) VALUES
(1, 14, 1, '2026-09-30 18:04:05', '2026-09-30 23:50:57', 'pabeigta'),
(3, 11, 1, '2026-09-30 19:31:12', NULL, 'atcelta'),
(4, 10, 1, '2026-09-30 19:31:12', NULL, 'pabeigta'),
(5, 11, 1, '2026-10-01 00:05:00', '2026-10-01 00:08:21', 'pabeigta'),
(6, 13, 1, '2026-10-01 00:10:34', NULL, 'atcelta'),
(8, 15, 1, '2026-10-01 00:21:59', NULL, 'atcelta'),
(9, 15, 1, '2026-10-01 00:27:01', '2026-10-01 00:27:05', 'pabeigta'),
(10, 12, 5, '2026-10-01 12:22:03', NULL, 'atcelta'),
(11, 15, 1, '2026-10-01 12:22:31', NULL, 'atcelta'),
(12, 12, 5, '2026-10-01 13:22:39', NULL, 'atcelta'),
(13, 13, 6, '2026-10-01 13:53:07', NULL, 'atcelta'),
(14, 10, 7, '2026-10-01 13:54:25', NULL, 'atcelta'),
(15, 15, 7, '2026-10-01 16:21:33', NULL, 'atcelta'),
(16, 14, 7, '2026-10-01 16:24:04', NULL, 'atcelta'),
(17, 13, 7, '2026-10-01 16:24:56', NULL, 'atcelta'),
(18, 11, 8, '2026-10-01 16:25:25', NULL, 'atcelta'),
(19, 14, 7, '2026-10-01 16:35:29', '2026-10-02 12:18:29', 'pabeigta'),
(20, 15, 7, '2026-10-01 16:56:36', NULL, 'aktīva'),
(21, 12, 7, '2026-10-01 16:58:14', '2026-10-02 10:37:16', 'pabeigta'),
(22, 11, 11, '2026-10-02 09:51:18', NULL, 'atcelta'),
(23, 11, 11, '2026-10-02 09:51:20', NULL, 'atcelta'),
(24, 10, 12, '2026-10-02 09:53:53', NULL, 'aktīva'),
(25, 13, 13, '2026-10-02 09:59:43', '2026-10-02 12:14:22', 'pabeigta'),
(26, 3, 7, '2026-10-02 11:39:13', NULL, 'atcelta'),
(27, 12, 14, '2026-10-02 12:13:33', '2026-10-02 12:14:23', 'pabeigta'),
(28, 6, 15, '2026-10-02 12:14:09', NULL, 'atcelta'),
(29, 1, 16, '2026-10-02 12:15:28', NULL, 'atcelta'),
(30, 11, 17, '2026-10-02 12:15:55', NULL, 'atcelta'),
(31, 13, 18, '2026-10-02 12:16:27', NULL, 'aktīva'),
(32, 4, 19, '2026-10-02 12:16:48', NULL, 'gaida'),
(33, 5, 20, '2026-10-02 12:17:39', NULL, 'atcelta'),
(34, 3, 21, '2026-10-02 12:17:57', '2026-10-02 12:18:25', 'pabeigta'),
(35, 12, 22, '2026-10-02 12:19:03', NULL, 'gaida');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_zurnals`
--
ALTER TABLE `audit_zurnals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `darbinieki`
--
ALTER TABLE `darbinieki`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gramatas`
--
ALTER TABLE `gramatas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lasitaji`
--
ALTER TABLE `lasitaji`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rezervacijas`
--
ALTER TABLE `rezervacijas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lasitajs_id` (`lasitajs_id`),
  ADD KEY `gramata_id` (`gramata_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_zurnals`
--
ALTER TABLE `audit_zurnals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `darbinieki`
--
ALTER TABLE `darbinieki`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gramatas`
--
ALTER TABLE `gramatas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lasitaji`
--
ALTER TABLE `lasitaji`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rezervacijas`
--
ALTER TABLE `rezervacijas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rezervacijas`
--
ALTER TABLE `rezervacijas`
  ADD CONSTRAINT `rezervacijas_ibfk_1` FOREIGN KEY (`gramata_id`) REFERENCES `gramatas` (`id`),
  ADD CONSTRAINT `rezervacijas_ibfk_2` FOREIGN KEY (`lasitajs_id`) REFERENCES `lasitaji` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
