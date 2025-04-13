-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-04-13 17:28:01
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `dbsdemo`
--

-- --------------------------------------------------------

--
-- 資料表結構 `blockedperiod`
--

CREATE TABLE `blockedperiod` (
  `block_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `blockedperiod`
--

INSERT INTO `blockedperiod` (`block_id`, `room_id`, `start_time`, `end_time`, `reason`) VALUES
(1, 1, '2025-04-17 00:00:00', '2025-04-30 00:00:00', '施工');

-- --------------------------------------------------------

--
-- 資料表結構 `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `sent_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `status` enum('reserved','cancelled') NOT NULL DEFAULT 'reserved',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `user_id`, `seat_id`, `date`, `time_slot`, `status`, `start_time`, `end_time`) VALUES
(1, 3, 112, '2025-04-12', '', 'reserved', '09:00:00', '11:00:00'),
(2, 3, 6, '2025-04-13', '', 'reserved', '08:00:00', '09:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `seat`
--

CREATE TABLE `seat` (
  `seat_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `has_power` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('available','reserved','not_seat') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `seat`
--

INSERT INTO `seat` (`seat_id`, `room_id`, `has_power`, `status`) VALUES
(1, 1, 0, 'available'),
(2, 1, 0, 'available'),
(3, 1, 0, 'available'),
(4, 1, 0, 'available'),
(5, 1, 0, 'available'),
(6, 1, 0, 'available'),
(7, 1, 0, 'available'),
(8, 1, 0, 'available'),
(9, 1, 0, 'available'),
(10, 1, 0, 'available'),
(11, 1, 0, 'available'),
(12, 1, 0, 'available'),
(13, 1, 0, 'available'),
(14, 1, 0, 'available'),
(15, 1, 0, 'available'),
(16, 1, 0, 'available'),
(17, 1, 0, 'available'),
(18, 1, 0, 'available'),
(19, 1, 0, 'available'),
(20, 1, 0, 'available'),
(21, 1, 0, 'available'),
(22, 1, 0, 'available'),
(23, 1, 0, 'available'),
(24, 1, 0, 'available'),
(25, 1, 0, 'available'),
(26, 1, 0, 'available'),
(27, 1, 0, 'available'),
(28, 1, 0, 'available'),
(29, 1, 0, 'available'),
(30, 1, 0, 'available'),
(31, 1, 0, 'available'),
(32, 1, 0, 'available'),
(33, 1, 0, 'available'),
(34, 1, 0, 'available'),
(35, 1, 0, 'available'),
(36, 1, 0, 'available'),
(37, 1, 0, 'available'),
(38, 1, 0, 'available'),
(39, 1, 0, 'available'),
(40, 1, 0, 'available'),
(41, 1, 0, 'available'),
(42, 1, 0, 'available'),
(43, 1, 0, 'available'),
(44, 1, 0, 'available'),
(45, 1, 0, 'available'),
(46, 1, 0, 'available'),
(47, 1, 0, 'available'),
(48, 1, 0, 'available'),
(49, 1, 0, 'available'),
(50, 1, 0, 'available'),
(51, 2, 0, 'available'),
(52, 2, 0, 'available'),
(53, 2, 0, 'available'),
(54, 2, 0, 'available'),
(55, 2, 0, 'available'),
(56, 2, 0, 'available'),
(57, 2, 0, 'available'),
(58, 2, 0, 'available'),
(59, 2, 0, 'available'),
(60, 2, 0, 'available'),
(61, 2, 0, 'available'),
(62, 2, 0, 'available'),
(63, 2, 0, 'available'),
(64, 2, 0, 'available'),
(65, 2, 0, 'available'),
(66, 2, 0, 'available'),
(67, 2, 0, 'available'),
(68, 2, 0, 'available'),
(69, 2, 0, 'available'),
(70, 2, 0, 'available'),
(71, 2, 0, 'available'),
(72, 2, 0, 'available'),
(73, 2, 0, 'available'),
(74, 2, 0, 'available'),
(75, 2, 0, 'available'),
(76, 2, 0, 'available'),
(77, 2, 0, 'available'),
(78, 2, 0, 'available'),
(79, 2, 0, 'available'),
(80, 2, 0, 'available'),
(81, 2, 0, 'available'),
(82, 2, 0, 'available'),
(83, 2, 0, 'available'),
(84, 2, 0, 'available'),
(85, 2, 0, 'available'),
(86, 2, 0, 'available'),
(87, 2, 0, 'available'),
(88, 2, 0, 'available'),
(89, 2, 0, 'available'),
(90, 2, 0, 'available'),
(91, 2, 0, 'available'),
(92, 2, 0, 'available'),
(93, 2, 0, 'available'),
(94, 2, 0, 'available'),
(95, 2, 0, 'available'),
(96, 2, 0, 'available'),
(97, 2, 0, 'available'),
(98, 2, 0, 'available'),
(99, 2, 0, 'available'),
(100, 2, 0, 'available'),
(110, 3, 0, 'available'),
(111, 3, 0, 'available'),
(112, 3, 0, 'available'),
(113, 3, 1, 'available'),
(114, 3, 0, 'available'),
(115, 3, 0, 'available'),
(116, 3, 0, 'not_seat'),
(117, 3, 0, 'available'),
(118, 3, 1, 'available'),
(119, 3, 0, 'available'),
(120, 3, 0, 'available'),
(121, 3, 0, 'available'),
(122, 3, 0, 'available'),
(123, 3, 1, 'available'),
(124, 3, 0, 'available'),
(125, 3, 0, 'available'),
(126, 3, 0, 'available'),
(127, 3, 1, 'available'),
(128, 3, 0, 'available'),
(129, 3, 0, 'available'),
(130, 3, 0, 'not_seat'),
(131, 3, 0, 'available'),
(132, 3, 0, 'available'),
(133, 3, 0, 'available'),
(134, 3, 0, 'available');

-- --------------------------------------------------------

--
-- 資料表結構 `studyroom`
--

CREATE TABLE `studyroom` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `row_count` int(11) NOT NULL DEFAULT 5,
  `col_count` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `studyroom`
--

INSERT INTO `studyroom` (`room_id`, `room_name`, `location`, `row_count`, `col_count`) VALUES
(1, '301', '', 5, 10),
(2, '自1', '301', 5, 10),
(3, '22', '22', 5, 5);

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(60) NOT NULL,
  `updateTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `name`, `email`, `updateTime`, `role`) VALUES
(1, 'TEST', '$2b$12$mbsJXEyKEmozZ/5RbVHjMOaXadg0lmzZfguPqFpKAr7SZvOaXJahW', '測試帳號', 'Curtis@CansCurtis.com', '2024-02-29 11:54:21', 'user'),
(3, 'EDWARD', '$2y$10$wzmbZ.9B9KZNEcyxBj2RauOM/8pLFGGqWMhDy3oN/KtCS.sDfOWmG', 'ed', 'ed@ed', '2025-04-12 09:57:22', 'user'),
(4, 'ADMIN', '$2y$10$wzmbZ.9B9KZNEcyxBj2RauOM/8pLFGGqWMhDy3oN/KtCS.sDfOWmG', '管理員', 'admin@example.com', '2025-04-12 10:13:37', 'admin');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `blockedperiod`
--
ALTER TABLE `blockedperiod`
  ADD PRIMARY KEY (`block_id`),
  ADD KEY `room_id` (`room_id`);

--
-- 資料表索引 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- 資料表索引 `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- 資料表索引 `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `room_id` (`room_id`);

--
-- 資料表索引 `studyroom`
--
ALTER TABLE `studyroom`
  ADD PRIMARY KEY (`room_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `stuId` (`username`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `blockedperiod`
--
ALTER TABLE `blockedperiod`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `seat`
--
ALTER TABLE `seat`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `studyroom`
--
ALTER TABLE `studyroom`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `blockedperiod`
--
ALTER TABLE `blockedperiod`
  ADD CONSTRAINT `blockedperiod_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `studyroom` (`room_id`);

--
-- 資料表的限制式 `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`);

--
-- 資料表的限制式 `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`seat_id`);

--
-- 資料表的限制式 `seat`
--
ALTER TABLE `seat`
  ADD CONSTRAINT `seat_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `studyroom` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
