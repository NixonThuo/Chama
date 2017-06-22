-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2016 at 12:00 PM
-- Server version: 5.5.39
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `chama_group`
--

-- --------------------------------------------------------

--
-- Table structure for table `chama_acc`
--

CREATE TABLE IF NOT EXISTS `chama_acc` (
`id` int(11) NOT NULL,
  `acc_total` int(11) NOT NULL DEFAULT '0',
  `acc_bal` int(11) NOT NULL DEFAULT '0',
  `loan` int(11) NOT NULL DEFAULT '0',
  `project` int(11) NOT NULL DEFAULT '0',
  `bal` int(11) NOT NULL DEFAULT '0',
  `respo` varchar(60) NOT NULL DEFAULT 'clerk clerk',
  `to_pay_interest` int(11) NOT NULL DEFAULT '0',
  `payed_interest` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `chama_acc`
--

INSERT INTO `chama_acc` (`id`, `acc_total`, `acc_bal`, `loan`, `project`, `bal`, `respo`, `to_pay_interest`, `payed_interest`) VALUES
(3, 30000, 32318, -2318, 0, 0, 'clerk clerk', 2318, -2318);

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE IF NOT EXISTS `contributions` (
`id` int(11) NOT NULL,
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `id_no` int(11) NOT NULL,
  `amound` int(11) NOT NULL,
  `date_ex` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payed_to` varchar(60) NOT NULL,
  `payed_loan` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` (`id`, `fname`, `lname`, `id_no`, `amound`, `date_ex`, `payed_to`, `payed_loan`) VALUES
(63, 'antony', 'antony', 12345678, 5000, '2016-11-20 10:08:28', 'duncanmakewa', 0),
(64, 'antony', 'antony', 12345678, 5000, '2016-11-20 10:08:30', 'duncanmakewa', 0),
(65, 'antony', 'antony', 12345678, 5000, '2016-11-20 10:08:34', 'duncanmakewa', 0),
(66, 'duncan', 'makewa', 31688002, 5000, '2016-11-20 10:08:45', 'duncanmakewa', 0),
(67, 'duncan', 'makewa', 31688002, 5000, '2016-11-20 10:08:48', 'duncanmakewa', 0),
(68, 'duncan', 'makewa', 31688002, 5000, '2016-11-20 10:08:51', 'duncanmakewa', 0);

-- --------------------------------------------------------

--
-- Table structure for table `loan_request`
--

CREATE TABLE IF NOT EXISTS `loan_request` (
`id` int(11) NOT NULL,
  `names` varchar(60) NOT NULL,
  `id_no` int(11) NOT NULL,
  `borrow_date` date DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` date DEFAULT NULL,
  `request_amound` int(11) NOT NULL,
  `add_more` int(11) DEFAULT NULL,
  `approved` int(11) NOT NULL DEFAULT '0',
  `to_pay` int(11) NOT NULL,
  `payed` int(11) NOT NULL DEFAULT '0',
  `req_reason` varchar(60) NOT NULL,
  `decline_reason` varchar(60) DEFAULT NULL,
  `me` int(11) NOT NULL,
  `penalty` int(11) NOT NULL,
  `setted` int(11) NOT NULL DEFAULT '0',
  `award` int(11) NOT NULL,
  `t_id` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `loan_request`
--

INSERT INTO `loan_request` (`id`, `names`, `id_no`, `borrow_date`, `request_date`, `due_date`, `request_amound`, `add_more`, `approved`, `to_pay`, `payed`, `req_reason`, `decline_reason`, `me`, `penalty`, `setted`, `award`, `t_id`) VALUES
(50, 'antony antony', 12345678, NULL, '2016-11-20 10:09:43', NULL, 19320, NULL, 3, 0, 21638, 'bdgdfgdgddfgdf', 'gdfgdgdfgd', 1, 0, 1, 0, 12345679),
(51, 'antony antony', 12345678, '2016-11-20', '2016-11-20 10:10:27', '2016-11-30', 19320, NULL, 3, 0, 21638, 'gfghfghfghf', 'cgdfgdfgdfgd', 1, 0, 1, 19320, 12345681);

-- --------------------------------------------------------

--
-- Table structure for table `manage_contr`
--

CREATE TABLE IF NOT EXISTS `manage_contr` (
`id` int(11) NOT NULL,
  `today_ex` date NOT NULL,
  `next_cont` date NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `manage_contr`
--

INSERT INTO `manage_contr` (`id`, `today_ex`, `next_cont`) VALUES
(3, '2016-11-16', '2016-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `membership_fee`
--

CREATE TABLE IF NOT EXISTS `membership_fee` (
`id` int(11) NOT NULL,
  `mem_fname` varchar(60) NOT NULL,
  `mem_lname` varchar(60) NOT NULL,
  `mem_idno` int(11) NOT NULL,
  `amound` int(11) NOT NULL,
  `date_ex` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `clerk_name` varchar(60) NOT NULL,
  `next_date` date NOT NULL,
  `total_cont` int(11) NOT NULL,
  `no_of_cont` int(11) NOT NULL,
  `target` int(11) NOT NULL DEFAULT '12000',
  `loan` int(60) DEFAULT '0',
  `approve` int(11) NOT NULL DEFAULT '0',
  `loan_amound` int(11) NOT NULL,
  `t_id` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `membership_fee`
--

INSERT INTO `membership_fee` (`id`, `mem_fname`, `mem_lname`, `mem_idno`, `amound`, `date_ex`, `clerk_name`, `next_date`, `total_cont`, `no_of_cont`, `target`, `loan`, `approve`, `loan_amound`, `t_id`) VALUES
(9, 'antony', 'antony', 12345678, 500, '2016-11-20 10:07:33', 'duncan makewa', '0000-00-00', 15000, 3, 12000, 0, 0, 0, 12345682),
(10, 'duncan', 'makewa', 31688002, 500, '2016-11-20 10:07:37', 'duncan makewa', '0000-00-00', 15000, 3, 12000, 0, 0, 0, 31688002);

-- --------------------------------------------------------

--
-- Table structure for table `member_reg`
--

CREATE TABLE IF NOT EXISTS `member_reg` (
`id` int(11) NOT NULL,
  `f_name` varchar(60) NOT NULL,
  `l_name` varchar(60) NOT NULL,
  `id_no` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `gender` tinytext NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo` varchar(60) NOT NULL,
  `tel` int(11) NOT NULL,
  `access` int(11) NOT NULL DEFAULT '1',
  `category` varchar(60) NOT NULL DEFAULT 'new',
  `reg_fee` int(11) NOT NULL,
  `next_date` date NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `member_reg`
--

INSERT INTO `member_reg` (`id`, `f_name`, `l_name`, `id_no`, `email`, `gender`, `username`, `password`, `reg_date`, `photo`, `tel`, `access`, `category`, `reg_fee`, `next_date`) VALUES
(6, 'antony', 'antony', 12345678, 'anto@gmail.com', '1', 'anto', '1d7d66c901289d714228f527c340472e361d74a3', '2016-11-20 16:28:49', '../upload/Penguins.jpg', 706373977, 1, 'Existing', 500, '0000-00-00'),
(7, 'duncan', 'makewa', 31688002, 'd@gmail.com', '1', 'dun', '0778c4d9430894027c5b0a1268924222eba8cf02', '2016-11-20 16:31:11', '../upload/Jellyfish.jpg', 706373977, 1, 'Existing', 500, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `name` varchar(60) NOT NULL,
  `amount_req` int(11) NOT NULL,
  `allocated` int(11) NOT NULL,
  `desc` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
`id` int(11) NOT NULL,
  `bal` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`name`, `amount_req`, `allocated`, `desc`, `start_date`, `end_date`, `id`, `bal`, `total`) VALUES
('boda boda', 5000, 3000, 'vvvvvvvvvvvvvvvvv', '2016-11-01', '2016-11-30', 3, 3000, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `p_allocation`
--

CREATE TABLE IF NOT EXISTS `p_allocation` (
`id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `f_name` varchar(60) NOT NULL,
  `l_name` varchar(60) NOT NULL,
  `id_no` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `access` int(11) NOT NULL DEFAULT '0',
  `all` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `f_name`, `l_name`, `id_no`, `username`, `password`, `reg_date`, `access`, `all`) VALUES
(2, 'admin', 'admin', 222222222, 'admin', 'admin', '2016-11-17 14:33:38', 1, 1),
(3, 'duncan', 'makewa', 31688002, 'clerk', '6ba4b007cbacd051a265254c01c9f647c1c9a32b', '2016-11-20 15:25:22', 0, 0),
(4, 'clerk', 'clerk', 12345678, 'clerk', '7c222fb2927d828af22f592134e8932480637c0d', '2016-11-20 16:29:28', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chama_acc`
--
ALTER TABLE `chama_acc`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_request`
--
ALTER TABLE `loan_request`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manage_contr`
--
ALTER TABLE `manage_contr`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership_fee`
--
ALTER TABLE `membership_fee`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `mem_idno` (`mem_idno`);

--
-- Indexes for table `member_reg`
--
ALTER TABLE `member_reg`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_no` (`id_no`,`email`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p_allocation`
--
ALTER TABLE `p_allocation`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_no` (`id_no`,`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chama_acc`
--
ALTER TABLE `chama_acc`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `contributions`
--
ALTER TABLE `contributions`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `loan_request`
--
ALTER TABLE `loan_request`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `manage_contr`
--
ALTER TABLE `manage_contr`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `membership_fee`
--
ALTER TABLE `membership_fee`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `member_reg`
--
ALTER TABLE `member_reg`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `p_allocation`
--
ALTER TABLE `p_allocation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
