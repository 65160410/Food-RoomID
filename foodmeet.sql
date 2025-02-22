-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2025 at 03:22 PM
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
-- Database: `foodmeet`
--

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `ItemID` int(11) NOT NULL,
  `RestaurantID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `ItemName` varchar(255) NOT NULL,
  `ImageURL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`ItemID`, `RestaurantID`, `Description`, `Price`, `ItemName`, `ImageURL`) VALUES
(1, 1, 'ข้าวผัดหมู ไข่ดาว', 50.00, 'ข้าวผัดหมู', NULL),
(2, 1, 'ผัดกะเพราหมูสับ ไข่ดาว', 50.00, 'กะเพราหมูสับ', NULL),
(3, 1, 'หมูทอดกระเทียมพริกไทย', 50.00, 'หมูทอดกระเทียม', NULL),
(4, 1, 'ข้าวผัดกุ้ง', 60.00, 'ข้าวผัดกุ้ง', NULL),
(5, 1, 'ผัดพริกแกงไก่', 50.00, 'ผัดพริกแกงไก่', NULL),
(6, 2, 'แกงเขียวหวานไก่', 40.00, 'แกงเขียวหวาน', NULL),
(7, 2, 'แกงส้มชะอมไข่', 45.00, 'แกงส้มชะอมไข่', NULL),
(8, 2, 'ผัดเผ็ดหมู', 45.00, 'ผัดเผ็ดหมู', NULL),
(9, 2, 'ต้มจืดเต้าหู้หมูสับ', 40.00, 'ต้มจืดเต้าหู้', NULL),
(10, 2, 'พะแนงหมู', 45.00, 'พะแนงหมู', NULL),
(11, 3, 'ก๋วยเตี๋ยวเรือหมูน้ำตก', 40.00, 'ก๋วยเตี๋ยวเรือหมู', NULL),
(12, 3, 'ก๋วยเตี๋ยวเรือเนื้อน้ำตก', 45.00, 'ก๋วยเตี๋ยวเรือเนื้อ', NULL),
(13, 3, 'ก๋วยเตี๋ยวต้มยำไข่', 50.00, 'ก๋วยเตี๋ยวต้มยำไข่', NULL),
(14, 3, 'เกาเหลาเนื้อเปื่อย', 50.00, 'เกาเหลาเนื้อเปื่อย', NULL),
(15, 3, 'เส้นหมี่ลูกชิ้นน้ำใส', 40.00, 'เส้นหมี่ลูกชิ้นน้ำใส', NULL),
(16, 4, 'ข้าวมันไก่ต้ม', 50.00, 'ข้าวมันไก่ต้ม', NULL),
(17, 4, 'ข้าวมันไก่ทอด', 50.00, 'ข้าวมันไก่ทอด', NULL),
(18, 4, 'ข้าวมันไก่รวม', 55.00, 'ข้าวมันไก่รวม', NULL),
(19, 4, 'ไก่ทอดจานเล็ก', 60.00, 'ไก่ทอด', NULL),
(20, 4, 'ตับไก่ลวก', 30.00, 'ตับไก่ลวก', NULL),
(21, 5, 'ราดหน้าหมู', 50.00, 'ราดหน้าหมู', NULL),
(22, 5, 'ราดหน้าทะเล', 60.00, 'ราดหน้าทะเล', NULL),
(23, 5, 'ผัดซีอิ๊วหมู', 50.00, 'ผัดซีอิ๊วหมู', NULL),
(24, 5, 'เส้นใหญ่ผัดขี้เมา', 50.00, 'เส้นใหญ่ผัดขี้เมา', NULL),
(25, 5, 'คั่วไก่', 55.00, 'คั่วไก่', NULL),
(26, 6, 'ข้าวต้มหมู', 40.00, 'ข้าวต้มหมู', NULL),
(27, 6, 'ข้าวต้มปลา', 50.00, 'ข้าวต้มปลา', NULL),
(28, 6, 'ข้าวต้มทะเล', 55.00, 'ข้าวต้มทะเล', NULL),
(29, 6, 'ต้มยำกุ้ง', 60.00, 'ต้มยำกุ้ง', NULL),
(30, 6, 'ยำไข่เค็ม', 40.00, 'ยำไข่เค็ม', NULL),
(31, 7, 'ข้าวไข่เจียวหมูสับ', 35.00, 'ข้าวไข่เจียวหมูสับ', NULL),
(32, 7, 'ข้าวไข่เจียวกุ้งสับ', 40.00, 'ข้าวไข่เจียวกุ้งสับ', NULL),
(33, 7, 'ไข่เจียวแหนม', 40.00, 'ไข่เจียวแหนม', NULL),
(34, 7, 'ไข่เจียวหมูยอ', 40.00, 'ไข่เจียวหมูยอ', NULL),
(35, 7, 'ข้าวไข่ข้นกุ้ง', 50.00, 'ข้าวไข่ข้นกุ้ง', NULL),
(36, 8, 'โจ๊กหมู', 40.00, 'โจ๊กหมู', NULL),
(37, 8, 'โจ๊กหมูไข่ลวก', 45.00, 'โจ๊กหมูไข่ลวก', NULL),
(38, 8, 'โจ๊กตับหมู', 45.00, 'โจ๊กตับหมู', NULL),
(39, 8, 'โจ๊กไก่ฉีก', 45.00, 'โจ๊กไก่ฉีก', NULL),
(40, 8, 'ปาท่องโก๋', 20.00, 'ปาท่องโก๋', NULL),
(41, 9, 'ส้มตำไทย', 50.00, 'ส้มตำไทย', NULL),
(42, 9, 'ส้มตำปูปลาร้า', 50.00, 'ส้มตำปูปลาร้า', NULL),
(43, 9, 'ส้มตำไข่เค็ม', 55.00, 'ส้มตำไข่เค็ม', NULL),
(44, 9, 'ไก่ย่างครึ่งตัว', 80.00, 'ไก่ย่าง', NULL),
(45, 9, 'คอหมูย่าง', 70.00, 'คอหมูย่าง', NULL),
(46, 10, 'ข้าวหมูแดง', 50.00, 'ข้าวหมูแดง', NULL),
(47, 10, 'ข้าวหมูกรอบ', 50.00, 'ข้าวหมูกรอบ', NULL),
(48, 10, 'ข้าวหมูแดงหมูกรอบ', 55.00, 'ข้าวหมูแดงหมูกรอบ', NULL),
(49, 10, 'เกาเหลาหมูกรอบ', 55.00, 'เกาเหลาหมูกรอบ', NULL),
(50, 10, 'ไข่ต้มยางมะตูม', 15.00, 'ไข่ต้มยางมะตูม', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menupreferences`
--

CREATE TABLE `menupreferences` (
  `UserID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `PreferenceType` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `RestaurantID` int(11) NOT NULL,
  `Description` text DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `AverageRating` decimal(4,2) DEFAULT NULL,
  `PopularityScore` int(11) DEFAULT NULL,
  `RestaurantName` varchar(255) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Website` varchar(255) DEFAULT NULL,
  `CuisineType` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`RestaurantID`, `Description`, `Location`, `AverageRating`, `PopularityScore`, `RestaurantName`, `Address`, `Phone`, `Website`, `CuisineType`) VALUES
(1, 'ร้านอาหารตามสั่ง อร่อย ราคาถูก', 'บางแสน, ชลบุรี', 4.50, 90, 'เจ๊น้อยอาหารตามสั่ง', '123/4 ถนนเลียบชายหาด, บางแสน', '081-234-5678', NULL, 'Thai'),
(2, 'ข้าวแกงรสเด็ด เมนูหลากหลาย', 'บางแสน, ชลบุรี', 4.60, 88, 'ข้าวแกงป้าแดง', '567/8 ถนนสุขุมวิท, บางแสน', '082-345-6789', NULL, 'Thai'),
(3, 'ก๋วยเตี๋ยวเรือ น้ำซุปเข้มข้น', 'บางแสน, ชลบุรี', 4.40, 87, 'ก๋วยเตี๋ยวเรือบางแสน', '234/5 ถนนบางแสนสาย 2', '089-456-7890', NULL, 'Noodles'),
(4, 'ข้าวมันไก่ สูตรเด็ด น้ำจิ้มสุดแซ่บ', 'บางแสน, ชลบุรี', 4.50, 85, 'ข้าวมันไก่เฮียตี๋', '678/9 ซอย 10 ถนนบางแสน', '090-567-8901', NULL, 'Thai'),
(5, 'ราดหน้า ผัดซีอิ๊ว เส้นเหนียวนุ่ม', 'บางแสน, ชลบุรี', 4.30, 83, 'ราดหน้าเจ๊หมวย', '345/6 ถนนสุขุมวิท', '091-678-9012', NULL, 'Thai'),
(6, 'ข้าวต้มเครื่อง เมนูโต้รุ่ง', 'บางแสน, ชลบุรี', 4.40, 82, 'ข้าวต้มรุ่งโรจน์', '123/6 ถนนบางแสน-อ่างศิลา', '092-789-0123', NULL, 'Thai'),
(7, 'ข้าวไข่เจียว เมนูง่ายๆ อร่อย', 'บางแสน, ชลบุรี', 4.20, 80, 'ไข่เจียวบางแสน', '456/7 ถนนสุขุมวิท', '093-890-1234', NULL, 'Thai'),
(8, 'โจ๊กหมูร้อนๆ พร้อมไข่ลวก', 'บางแสน, ชลบุรี', 4.50, 86, 'โจ๊กหมูอาม่า', '789/8 ซอย 3 ถนนสุขุมวิท', '094-901-2345', NULL, 'Thai'),
(9, 'ส้มตำ-ไก่ย่าง รสแซ่บ', 'บางแสน, ชลบุรี', 4.70, 89, 'ตำแซ่บบางแสน', '567/8 ถนนบางแสน-อ่างศิลา', '095-012-3456', NULL, 'Thai'),
(10, 'ข้าวหมูแดง-หมูกรอบ สูตรโบราณ', 'บางแสน, ชลบุรี', 4.60, 88, 'ข้าวหมูแดงบางแสน', '789/9 ถนนบางแสนสาย 2', '096-123-4567', NULL, 'Thai');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `RestaurantID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL,
  `Comment` text DEFAULT NULL,
  `ReviewTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roommembers`
--

CREATE TABLE `roommembers` (
  `RoomID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `JoinedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roommembers`
--

INSERT INTO `roommembers` (`RoomID`, `UserID`, `JoinedAt`) VALUES
(105, 13, '2025-02-22 12:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` int(11) NOT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `RoomCode` varchar(20) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `guestName` varchar(255) DEFAULT NULL,
  `guestAvatar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `CreatedBy`, `CreatedAt`, `RoomCode`, `room_name`, `description`, `guestName`, `guestAvatar`) VALUES
(105, 21, '2025-02-22 12:00:51', 'WYBALM', 'sek123', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Preferences` text DEFAULT NULL,
  `DietaryRestrictions` text DEFAULT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT NULL,
  `isGuest` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Preferences`, `DietaryRestrictions`, `Username`, `Password`, `Email`, `Avatar`, `isGuest`) VALUES
(13, NULL, NULL, 'Seksant Sukkasem', '1234', 'admin1234@gmail.com', NULL, NULL),
(21, NULL, NULL, 'Seksant', '123', 'admin@go.buu.ac.th', NULL, NULL),
(90, NULL, NULL, 'ดั้ง', '', NULL, 'https://i.pravatar.cc/150?img=1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `UserID` int(11) NOT NULL,
  `RoomID` int(11) NOT NULL,
  `RestaurantID` int(11) NOT NULL,
  `VoteTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `foodName` varchar(255) NOT NULL,
  `vote` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `RestaurantID` (`RestaurantID`);

--
-- Indexes for table `menupreferences`
--
ALTER TABLE `menupreferences`
  ADD PRIMARY KEY (`UserID`,`ItemID`),
  ADD KEY `ItemID` (`ItemID`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`RestaurantID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `RestaurantID` (`RestaurantID`);

--
-- Indexes for table `roommembers`
--
ALTER TABLE `roommembers`
  ADD PRIMARY KEY (`RoomID`,`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`RoomID`),
  ADD UNIQUE KEY `RoomCode` (`RoomCode`),
  ADD KEY `CreatedBy` (`CreatedBy`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`UserID`,`RoomID`,`RestaurantID`),
  ADD UNIQUE KEY `unq_user_room_restaurant` (`UserID`,`RoomID`,`RestaurantID`,`foodName`),
  ADD KEY `RoomID` (`RoomID`),
  ADD KEY `RestaurantID` (`RestaurantID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `RestaurantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD CONSTRAINT `menuitems_ibfk_1` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`) ON DELETE CASCADE;

--
-- Constraints for table `menupreferences`
--
ALTER TABLE `menupreferences`
  ADD CONSTRAINT `menupreferences_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `menupreferences_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `menuitems` (`ItemID`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`) ON DELETE CASCADE;

--
-- Constraints for table `roommembers`
--
ALTER TABLE `roommembers`
  ADD CONSTRAINT `roommembers_ibfk_1` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`) ON DELETE CASCADE,
  ADD CONSTRAINT `roommembers_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
