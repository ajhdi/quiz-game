-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 11:06 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `prof_tbl`
--

CREATE TABLE `prof_tbl` (
  `profID` int(11) NOT NULL,
  `proFname` varchar(50) DEFAULT NULL,
  `proLname` varchar(50) DEFAULT NULL,
  `proMname` varchar(50) DEFAULT NULL,
  `profNo` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prof_tbl`
--

INSERT INTO `prof_tbl` (`profID`, `proFname`, `proLname`, `proMname`, `profNo`, `email`, `password`) VALUES
(1, 'Joselito', 'Tinambacan', 'Brequillo', '2020-88521-MM-0', 'joselito.tinambacan@gmail.com', '$2y$10$qD/sS/Jf9ldHBHXVRr8XG.5RD9QhFHK1Pi4PM54FL9UuczScRfeKC'),
(4, 'Joselito', 'Tinambacan', 'Brequillo', '2020-88521-MM-1', 'joselito.tinambacan1@gmail.com', '$2y$10$5cWO0wM6SQ8kf4Ek.gTwjuj0c/pYxeErSJ4NbKTReRt77ohCw5dZ2');

-- --------------------------------------------------------

--
-- Table structure for table `question_tbl`
--

CREATE TABLE `question_tbl` (
  `questionID` int(11) NOT NULL,
  `questionDesc` text DEFAULT NULL,
  `optionA` varchar(255) DEFAULT NULL,
  `optionB` varchar(255) DEFAULT NULL,
  `optionC` varchar(255) DEFAULT NULL,
  `optionD` varchar(255) DEFAULT NULL,
  `correctAnswer` varchar(255) DEFAULT NULL,
  `quizID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_tbl`
--

CREATE TABLE `quiz_tbl` (
  `quizID` int(11) NOT NULL,
  `quizTitle` varchar(100) DEFAULT NULL,
  `subjectDesc` varchar(100) DEFAULT NULL,
  `subjectCode` varchar(20) DEFAULT NULL,
  `courseCode` varchar(255) NOT NULL,
  `yearSection` varchar(20) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT 1,
  `profID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_tbl`
--

INSERT INTO `quiz_tbl` (`quizID`, `quizTitle`, `subjectDesc`, `subjectCode`, `courseCode`, `yearSection`, `isActive`, `profID`) VALUES
(1, 'Quiz 1', 'Math', 'COMP', 'BSIT3', '1-1', 1, 1),
(2, 'Quiz 2', 'PE', 'PATHFIT', 'BSIT', '2-2', 1, 1),
(3, 'Quiz 3', 'Math', 'COMP', 'BSIT3', '2-2', 1, 1),
(4, 'Quiz 4', 'PE', 'COMP', 'BSIT3', '2-2', 1, 1),
(5, 'Quiz 5', 'PE', 'PATHFIT', 'BSIT3', '1-1', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `result_tbl`
--

CREATE TABLE `result_tbl` (
  `resultID` int(11) NOT NULL,
  `quizID` int(11) DEFAULT NULL,
  `studentID` int(11) DEFAULT NULL,
  `scores` int(11) DEFAULT NULL,
  `totalScores` int(11) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_tbl`
--

CREATE TABLE `student_tbl` (
  `studentID` int(11) NOT NULL,
  `studFname` varchar(50) DEFAULT NULL,
  `studLname` varchar(50) DEFAULT NULL,
  `studMname` varchar(50) DEFAULT NULL,
  `studNo` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `courseCode` varchar(20) DEFAULT NULL,
  `yearSection` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tbl`
--

INSERT INTO `student_tbl` (`studentID`, `studFname`, `studLname`, `studMname`, `studNo`, `email`, `password`, `courseCode`, `yearSection`) VALUES
(1, 'Arvine', 'Dimaano', 'Hernandez', '2023-00547-TG-1', 'sample1@gmail.com', '$2y$10$I/J8GTonTZohgUY6HWim1OA8klO1Shal.FuLp97PFExVwRJ7AWKJK', 'BSIT', '1-1'),
(2, 'Joselito', 'Tinambacan', 'Brequillo', '12341234', 'joselito.tinambacan@gmail.com', '$2y$10$NUfLcrYvdvAZ/H/SroMV8.JT7FJ/4RqwtVbpQ6P3lvsO1yJtjzMoq', 'BSIT', '2-2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prof_tbl`
--
ALTER TABLE `prof_tbl`
  ADD PRIMARY KEY (`profID`),
  ADD UNIQUE KEY `profNo` (`profNo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `question_tbl`
--
ALTER TABLE `question_tbl`
  ADD PRIMARY KEY (`questionID`),
  ADD KEY `quizID` (`quizID`);

--
-- Indexes for table `quiz_tbl`
--
ALTER TABLE `quiz_tbl`
  ADD PRIMARY KEY (`quizID`),
  ADD KEY `profID` (`profID`);

--
-- Indexes for table `result_tbl`
--
ALTER TABLE `result_tbl`
  ADD PRIMARY KEY (`resultID`),
  ADD KEY `quizID` (`quizID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `student_tbl`
--
ALTER TABLE `student_tbl`
  ADD PRIMARY KEY (`studentID`),
  ADD UNIQUE KEY `studNo` (`studNo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `prof_tbl`
--
ALTER TABLE `prof_tbl`
  MODIFY `profID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `question_tbl`
--
ALTER TABLE `question_tbl`
  MODIFY `questionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_tbl`
--
ALTER TABLE `quiz_tbl`
  MODIFY `quizID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `result_tbl`
--
ALTER TABLE `result_tbl`
  MODIFY `resultID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_tbl`
--
ALTER TABLE `student_tbl`
  MODIFY `studentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `question_tbl`
--
ALTER TABLE `question_tbl`
  ADD CONSTRAINT `question_tbl_ibfk_1` FOREIGN KEY (`quizID`) REFERENCES `quiz_tbl` (`quizID`);

--
-- Constraints for table `quiz_tbl`
--
ALTER TABLE `quiz_tbl`
  ADD CONSTRAINT `quiz_tbl_ibfk_1` FOREIGN KEY (`profID`) REFERENCES `prof_tbl` (`profID`);

--
-- Constraints for table `result_tbl`
--
ALTER TABLE `result_tbl`
  ADD CONSTRAINT `result_tbl_ibfk_1` FOREIGN KEY (`quizID`) REFERENCES `quiz_tbl` (`quizID`),
  ADD CONSTRAINT `result_tbl_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student_tbl` (`studentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
