-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2025 at 03:56 PM
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
-- Database: `studentrecord`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_student_auto_increment` ()   BEGIN
    DECLARE next_id INT;
    SELECT IFNULL(MAX(studid), 0) + 1 INTO next_id FROM student;
    SET @sql = CONCAT('ALTER TABLE student AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studid` int(11) NOT NULL,
  `studName` varchar(100) NOT NULL,
  `studPic` longtext DEFAULT NULL,
  `studEmail` varchar(50) NOT NULL,
  `studPhone` varchar(11) NOT NULL,
  `studAddress` varchar(150) NOT NULL,
  `studCity` varchar(50) NOT NULL,
  `studState` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studid`, `studName`, `studPic`, `studEmail`, `studPhone`, `studAddress`, `studCity`, `studState`) VALUES
(1, 'Soo Xin Yun', '68a99648b47ee.jpg', 'xinyun@email.com', '0123456788', '72 Jalan Akasia, Taman Akasia Jaya', 'Sandakan', 'Sabah'),
(2, 'Alex Lim Chiang Shing', '68a9c705601cb.png', 'alexLim@email.com', '0123456789', '12 Jalan Melati 1, Taman Melati', 'Kuala Lumpur', 'Wilayah Persekutuan'),
(3, 'Christine Ooi Yan Ming', '68a9c7b04cf60.png', 'christineOoi@email.com', '0134567890', '45 Jalan Kenanga, Taman Sri Hartamas', 'Petaling Jaya', 'Selangor'),
(4, 'Tan Wei Ming', '68a9c7c6775bc.png', 'weiMing@email.com', '01123456789', '78 Jalan Bunga Raya, Taman Bunga Raya', 'Johor Bahru', 'Johor'),
(5, 'Elestine Teoh Ching Yan', '68a9c7cfa2b70.png', 'elestine233@email.com', '0178901234', '23 Lorong Mawar 2, Taman Mawar', 'George Town', 'Pulau Pinang'),
(6, 'Lim Chee Wei', '68a9c7d75eb13.png', 'cheewei125@email.com', '0191234567', '56 Jalan Angsana, Taman Angsana Jaya', 'Ipoh', 'Perak'),
(7, 'Lee Jia Hao', '68a9c7df0045f.png', 'jiahao568@email.com', '0145678901', '89 Jalan Cempaka, Taman Cempaka Indah', 'Kota Kinabalu', 'Sabah'),
(8, 'Teo Boon Keat', '68a9c7f494865.png', 'boonkeat298@email.com', '0167890123', '101 Jalan Teratai, Taman Teratai', 'Kuching', 'Sarawak'),
(9, 'Chan Xin Yi', '68a9c7fd1757c.png', 'xinyi896@email.com', '0189012345', '34 Jalan Dahlia, Taman Dahlia', 'Shah Alam', 'Selangor'),
(10, 'Lau Wei Ming', '68a9c80405b81.png', 'weiming2rr@email.com', '0120987654', '67 Jalan Orkid, Taman Orkid Jaya', 'Malacca City', 'Melaka'),
(11, 'Goh Hui Shan', '68a9c80f2ac25.png', 'huishan@email.com', '0132109876', '90 Jalan Seroja, Taman Seroja Baru', 'Kuantan', 'Pahang'),
(12, 'Ho Kah Wei', NULL, 'kahwei@email.com', '01198765432', '15 Jalan Kemuning, Taman Kemuning', 'Seri Kembangan', 'Selangor'),
(13, 'Yap Xin Rou', NULL, 'xinrou@email.com', '0176543210', '28 Lorong Anggerik, Taman Anggerik Jaya', 'Alor Setar', 'Kedah'),
(14, 'Chew Jun Hao', NULL, 'junhao@email.com', '0198765432', '51 Jalan Kemboja, Taman Kemboja', 'Kangar', 'Perlis'),
(15, 'Ong Pei Ling', NULL, 'peiling@email.com', '0143210987', '84 Jalan Bunga Tanjung, Taman Bunga Tanjung', 'Kota Bharu', 'Kelantan'),
(16, 'Low Wei Shen', NULL, 'weishen@email.com', '0165432109', '17 Jalan Keladi, Taman Keladi Indah', 'Kuala Terengganu', 'Terengganu'),
(17, 'Tee Siew Mei', NULL, 'siewmei@email.com', '0187654321', '40 Jalan Meranti, Taman Meranti Jaya', 'Batu Pahat', 'Johor'),
(18, 'Khoo Boon Seng', NULL, 'boonseng@email.com', '0121098765', '63 Jalan Kembang, Taman Kembang Baru', 'Sibu', 'Sarawak'),
(19, 'Chin Hui Ting', NULL, 'huiting@email.com', '0138765432', '96 Jalan Cendana, Taman Cendana', 'Miri', 'Sarawak'),
(20, 'Liew Jia Wei', NULL, 'jiawei@email.com', '0174321098', '29 Jalan Kasturi, Taman Kasturi', 'Seremban', 'Negeri Sembilan'),
(21, 'Soo Xin Jie', NULL, 'xinjie@email.com', '01154321098', '72 Jalan Akasia, Taman Akasia Jaya', 'Sandakan', 'Sabah'),
(22, 'Hello DEF', NULL, 'abcd@gmail.com', '01234567891', '5245343434345543 abcde', 'abcdefg', 'abcdefge');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `pass` longtext NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(11) NOT NULL,
  `proPic` longtext DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1 - active\r\n0 - deleted',
  `level` int(1) NOT NULL DEFAULT 0 COMMENT '0 - member\r\n1 - admin',
  `superadmin` int(1) NOT NULL DEFAULT 0 COMMENT '0 - normal admin\r\n1 - superadmin',
  `remark` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `uname`, `pass`, `email`, `contact`, `proPic`, `status`, `level`, `superadmin`, `remark`) VALUES
(1, 'Teoh Kai Xin', 'e6a0a4ddc6257dad5a6160cbed5371b31923fcf6', 'teoh@gmail.com', '0162626262', '68a81ab8b355a.jpg', 1, 1, 1, 'Teoh123@'),
(2, 'Teoh', '4861ffec59dc9dcafabfdd6ecbde0038119707d1', 'hitem54041@cetnob.com', '0182938493', '66ec16b20143e.png', 1, 0, 0, '9jQ1Ibj5-P2'),
(3, 'teohmember', '9bc1c0bf7d4f25d2782ca9ee887ca81a5a832292', 'teohmember2@gmail.com', '0123365456', '68a8280b62a9d.png', 1, 0, 0, 'teohMem123!'),
(4, 'testing', 'c60ed1f0696c2a220aee8c2fb34902285d181640', 'test@gmail.com', '0123654789', '66e90eb0dadb3.png', 1, 1, 0, 'gXJ^WQRx-R9'),
(5, 'tests', '6ff1c6112f8f0ebe8bb7b3e89a3976e80113ded4', 'tests@gmail.com', '0147896523', '66e92e7867b03.png', 1, 1, 0, 'nyOoZ*LU*H2'),
(6, 'testrr', 'cad4a9a0c9632b3e34b19ba83e05b40228745523', 'test2rr@gmail.com', '0147896523', NULL, 1, 1, 1, 'z1y$7b!A%U1'),
(7, 'testrr', '896929858b6e3d465021be4399b3c9f7fdf2eb55', 'test2rr@gmail.com', '0147896523', NULL, 1, 1, 1, 'bYNe8PLx*D9'),
(8, 'teoh', 'd8d48206af16acbf7824a9cfaa9671e26ab09bbf', 'teohtest@gmail.com', '0147852369', '66e68a36710e4.jpg', 1, 1, 1, 'kAlpV@Rs!X0'),
(9, 'testqq', 'd3a7b75cad7de1ea48356b42da304a583b86b96e', 'dfsf@fds.fsd', '0192837465', NULL, 0, 1, 0, '!@^dZkR0$A7'),
(10, 'newcustomer', 'cdd2554d5ce7647fbf0d050bdbbdbba0671f0777', 'newcustomer@gmail.com', '0192849382', NULL, 1, 0, 0, 'ISbhuheu^L4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `studid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
