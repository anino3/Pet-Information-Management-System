-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2024 at 06:48 AM
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
-- Database: `petshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblmedi_mat`
--

CREATE TABLE `tblmedi_mat` (
  `medID` int(11) NOT NULL,
  `itemCode` int(11) NOT NULL,
  `itemName` varchar(100) NOT NULL,
  `typeName` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expirationDate` date NOT NULL,
  `itemPrice` int(11) NOT NULL,
  `itemImage` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblmedi_mat`
--

INSERT INTO `tblmedi_mat` (`medID`, `itemCode`, `itemName`, `typeName`, `quantity`, `expirationDate`, `itemPrice`, `itemImage`) VALUES
(10, 33214, 'Distilled Water', 'Hydration', 92, '2027-06-23', 120, 0x313430323230323431343234323370322e6a7067),
(11, 31244, 'PetArmor', 'Antibiotic', 0, '2025-06-09', 50, 0x323230323230323431323138343770312e6a7067),
(12, 412552, 'Syringe Tube', 'Medicine Tube', 76, '2027-10-26', 100, 0x3232303232303234313231393233737572676963616c2d73756374696f6e2d747562652d642e77656270),
(13, 142512, 'Dyxslezas', 'Hydration', 84, '2028-02-22', 200, 0x323230323230323431323230313370332e6a7067),
(15, 12411, 'Dextrose', 'Antibiotic', 1, '2025-03-30', 350, 0x303230333230323431363433313870312e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `tbloperation`
--

CREATE TABLE `tbloperation` (
  `id` int(11) NOT NULL,
  `operationType` varchar(100) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL,
  `details` varchar(10000) NOT NULL,
  `status` varchar(50) NOT NULL,
  `finishDate` date DEFAULT NULL,
  `finishTime` time DEFAULT NULL,
  `finishDetails` text DEFAULT NULL,
  `operationCost` int(11) NOT NULL,
  `medimat_used` text NOT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  `petID` int(11) DEFAULT NULL,
  `paymentID` int(11) DEFAULT NULL,
  `treatment_used` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbloperation`
--

INSERT INTO `tbloperation` (`id`, `operationType`, `time`, `date`, `details`, `status`, `finishDate`, `finishTime`, `finishDetails`, `operationCost`, `medimat_used`, `OwnerID`, `petID`, `paymentID`, `treatment_used`) VALUES
(277, 'Sof-tissue Operation', '16:57:00', '2024-03-04', 'NALIGSAN', 'Finished', '2024-03-04', '16:59:36', 'SUCCESS', 5500, 'Syringe Tube:3, Dyxslezas:3', 133, 137, 138, 'Anesthesia Machine, Infusion Pump, Cautery Device, Sphygmomanometer'),
(278, 'Orthopaedic Operation', '17:15:00', '2024-03-04', 'qweqwewe', 'Cancelled', '0000-00-00', '00:00:00', 'SUSPENDED', 2300, '', 130, 131, NULL, 'Anesthesia Machine, Infusion Pump'),
(279, 'Cardiovascular Operation', '20:08:00', '2024-03-04', 'NALIGSAN', 'On-going', NULL, NULL, NULL, 3060, 'Distilled Water:3, Dyxslezas:2', 130, 131, 140, 'Anesthesia Machine, Infusion Pump'),
(280, 'Orthopaedic Operation', '20:08:00', '2024-03-05', 'QWEQWE', 'On-going', NULL, NULL, NULL, 2900, 'Distilled Water:5', 130, 131, 142, 'Anesthesia Machine, Infusion Pump'),
(281, 'Sof-tissue Operation', '20:09:00', '2024-03-04', 'QWEQWE', 'Scheduled', NULL, NULL, NULL, 2300, '', 132, 134, 141, 'Anesthesia Machine, Infusion Pump');

-- --------------------------------------------------------

--
-- Table structure for table `tblowner`
--

CREATE TABLE `tblowner` (
  `OwnerID` int(11) NOT NULL,
  `OwnerName` varchar(255) DEFAULT NULL,
  `OwnerAddress` varchar(255) DEFAULT NULL,
  `OwnerCity` varchar(255) DEFAULT NULL,
  `OwnerZip` varchar(10) DEFAULT NULL,
  `OwnerMobileNo` varchar(15) DEFAULT NULL,
  `OwnerEmail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblowner`
--

INSERT INTO `tblowner` (`OwnerID`, `OwnerName`, `OwnerAddress`, `OwnerCity`, `OwnerZip`, `OwnerMobileNo`, `OwnerEmail`) VALUES
(129, 'Aling Nena', 'Qiapo Public Market', 'Qiapo City', '119', '0946450978', 'nena@gmail.com'),
(130, 'Naruto Uzumaki', 'Konoha', 'Konoha City', '69', '164328563', 'kakashi@gmail.com'),
(132, 'Nancy Binay', 'Makati House', 'Makati City', '911', '16161616161', 'arnoldcfrancia@gmail.com'),
(133, 'James Reid', '86 Rizal St.', '86 Rizal St.', '8000', '09204011019', 'b.francia.516403@umindanao.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `tblpayments`
--

CREATE TABLE `tblpayments` (
  `id` int(11) NOT NULL,
  `details` varchar(100) DEFAULT NULL,
  `amounts` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `petOwner` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayments`
--

INSERT INTO `tblpayments` (`id`, `details`, `amounts`, `date`, `user`, `petOwner`) VALUES
(122, 'Sof-tissue Operation', 2780.00, '2024-02-27', 'test1', 130),
(125, 'Cardiovascular Operation', 4350.00, '2024-02-27', 'tyson', 129),
(126, 'Sof-tissue Operation', 2050.00, '2024-02-28', 'tyson', 130),
(127, 'Vaccinations, Microchipping, Pet Groom and Wash', 5500.00, '2024-03-04', 'test1', 129),
(128, 'Cardiovascular Operation', 1000.00, '2024-03-04', 'test1', 129),
(129, 'Sof-tissue Operation', 1000.00, '2024-03-04', 'test1', 130),
(130, 'Cardiovascular Operation', 1650.00, '2024-03-04', 'test1', 133),
(131, 'Orthopaedic Operation', 1000.00, '2024-03-04', 'test1', 130),
(132, 'Sof-tissue Operation', 1000.00, '2024-03-04', 'test1', 129),
(133, 'Orthopaedic Operation', 4050.00, '2024-03-04', 'test1', 130),
(134, 'Orthopaedic Operation', 3150.00, '2024-03-04', 'test1', 132),
(135, 'Orthopaedic Operation', 2600.00, '2024-03-04', 'test1', 129),
(136, 'Sof-tissue Operation', 4050.00, '2024-03-04', 'test1', 130),
(137, 'Sof-tissue Operation', 1000.00, '2024-03-04', 'test1', 130),
(138, 'Sof-tissue Operation', 5500.00, '2024-03-04', 'test1', 133),
(139, 'Vaccinations', 2500.00, '2024-03-04', 'britz', 132),
(140, 'Cardiovascular Operation', 1500.00, '2024-03-04', 'test1', 130),
(141, 'Sof-tissue Operation', 1500.00, '2024-03-04', 'test1', 132),
(142, 'Orthopaedic Operation', 2900.00, '2024-03-04', 'test1', 130);

-- --------------------------------------------------------

--
-- Table structure for table `tblpet`
--

CREATE TABLE `tblpet` (
  `id` int(11) NOT NULL,
  `pet_name` varchar(255) DEFAULT NULL,
  `pet_type` varchar(255) DEFAULT NULL,
  `pet_breed` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `pet_notes` varchar(255) DEFAULT NULL,
  `picture` longblob DEFAULT NULL,
  `OwnerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpet`
--

INSERT INTO `tblpet` (`id`, `pet_name`, `pet_type`, `pet_breed`, `birthdate`, `age`, `gender`, `pet_notes`, `picture`, `OwnerID`) VALUES
(125, 'Sea', 'Turtle', 'Turtoise', '2024-02-25', 1, 'Asexual', 'qweqweqweqweqwe', 0x32353032323032343036353833323333323138343330315f353334373235363036383730383836325f353139353939343735363531333636303533315f6e2e6a7067, 125),
(130, 'Vegeta', 'Dragon', 'Vegetarian', '2024-02-25', 11, 'Female', 'qwqwdqwe', 0x3235303232303234313334323536313536303532375f3439363036303235373136373231335f313039323533313033325f6e2e6a7067, 129),
(131, 'Kakashi', 'Ninja', 'Akatsuki', '2024-02-25', 1, 'Female', 'qweqweqwe', 0x3235303232303234313334343337313034343337325f3339323632303631343137373834355f313436343737383334345f6e2e6a7067, 130),
(133, 'Binay', 'Crocodile', 'Nile Crocodile', '2024-02-26', 2, 'Asexual', 'BUAYA', 0x3236303232303234313430303537363539315f3737323034303933323930323437365f353935373133303030353535323131333039325f6e2e6a7067, 0),
(134, 'Binay', 'Crocodile', 'Nile Crocodile', '2024-02-27', 12, 'Female', 'BUAYA', 0x3237303232303234303532393334363539315f3737323034303933323930323437365f353935373133303030353535323131333039325f6e2e6a7067, 132),
(135, 'Tesla', 'Eel', 'Electric Eel', '2024-02-27', 1, 'Asexual', 'WE ARE ELECTRIC!', 0x323730323230323430353330333331323830323737345f3739373634313130373030393132355f333338353438323434373430363832333031345f6e2e6a7067, 132),
(137, 'Guko', 'Fox', 'Shitzu', '2024-03-04', 1, 'Female', 'qweqwe', 0x30343033323032343034333031333433303734343936355f313933373638303631393936303633315f353030363731373030373539343331383635345f6e2e6a7067, 133);

-- --------------------------------------------------------

--
-- Table structure for table `tblrecordservice`
--

CREATE TABLE `tblrecordservice` (
  `recordID` int(11) NOT NULL,
  `serviceTypes` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `totalCost` int(11) DEFAULT NULL,
  `petID` int(11) DEFAULT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  `paid` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblrecordservice`
--

INSERT INTO `tblrecordservice` (`recordID`, `serviceTypes`, `date`, `totalCost`, `petID`, `OwnerID`, `paid`) VALUES
(66, 'Vaccinations', '2024-02-26', 2500, 130, 129, 'true'),
(67, 'Wellness and sick exams, Vaccinations, Pest & Parasite Prevention', '2024-02-26', 7000, 131, 130, 'true'),
(68, 'Wellness and sick exams, Vaccinations, Pest & Parasite Prevention', '2024-02-27', 7000, 134, 132, 'true'),
(69, 'Vaccinations, Microchipping, Pet Groom and Wash', '2024-03-04', 5200, 130, 129, 'true'),
(70, 'Vaccinations', '2024-03-04', 2500, 134, 132, 'true');

-- --------------------------------------------------------

--
-- Table structure for table `tblservices`
--

CREATE TABLE `tblservices` (
  `serviceID` int(11) NOT NULL,
  `serviceName` text NOT NULL,
  `serviceDescription` text NOT NULL,
  `servicePrice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblservices`
--

INSERT INTO `tblservices` (`serviceID`, `serviceName`, `serviceDescription`, `servicePrice`) VALUES
(1, 'Wellness and sick exams', 'Our vets examine your pet from nose to tail—evaluating their overall health and recommending preventive care, diagnostics and treatments.', 3000),
(2, 'Vaccinations', 'Vaccinations are recommended for all dogs, cats and ferrets to help protect your pet from deadly infectious diseases.', 2500),
(3, 'Pest & Parasite Prevention', 'Help protect your pet from fleas, ticks, heartworms and other parasites year-round so they avoid deadly diseases.\r\n\r\n', 1500),
(4, 'Dental Care', 'Complete dental care diagnostics—including digital dental X-rays, anesthetized cleaning.', 500),
(5, 'Microchipping', 'A microchip could be the difference between your pet being lost and found.', 1500),
(6, 'Dermatology', 'Itchy skin and ears, allergies, infections and parasites are some of the most common health issues pets face that we can help treat.', 1500),
(7, 'Pet Groom and Wash', 'Pet grooming will involve shaving, clipping, or trimming, and wash.', 1200);

-- --------------------------------------------------------

--
-- Table structure for table `tbltreatmentplan`
--

CREATE TABLE `tbltreatmentplan` (
  `treatID` int(11) NOT NULL,
  `machineCode` int(11) NOT NULL,
  `machineName` text NOT NULL,
  `machineType` text NOT NULL,
  `machinePrice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltreatmentplan`
--

INSERT INTO `tbltreatmentplan` (`treatID`, `machineCode`, `machineName`, `machineType`, `machinePrice`) VALUES
(1, 110011, 'Anesthesia Machine', 'An anesthesia machine is a device that administers anesthetic agents.', 1000),
(2, 23447, 'Infusion Pump', 'Infusion pumps, also known as IV pumps, are devices that administer fluids, nutrients or medications directly into a patient\'s bloodstream. ', 1300),
(3, 35624, 'Electric Razor', 'An electric razor, also known as a clipper, is a device for cutting away hair. ', 500),
(4, 2725463, 'Cautery Device', 'A cautery device is any instrument used to burn tissue, particularly to seal wounds', 1200),
(5, 13240, 'Sphygmomanometer', 'A sphygmomanometer is an instrument that measures blood pressure.', 1100),
(6, 124734, 'Ultrasound Scanner', 'An ultrasound scanner is a machine that uses sound waves to visualize the internal structures of a body. ', 1100),
(7, 441552, 'X-ray Machine', 'An X-ray machine uses electromagnetic radiation to create imaging of solid structures within the body, particularly bones but also muscles and organs.', 2500);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(100) NOT NULL,
  `verification_code` text NOT NULL,
  `verified` int(11) NOT NULL,
  `reset_token` varchar(64) NOT NULL,
  `reset_token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `username`, `password`, `user_type`, `avatar`, `created_at`, `email`, `verification_code`, `verified`, `reset_token`, `reset_token_expiration`) VALUES
(26, 'test1', '$2y$10$QSoJRqTv1oMQuOAhi2LBV.H4jq80IGT5U9T2oY4GRu9Jy/qeigW0a', 'administrator', '020320241653196591_772040932902476_5957130005552113092_n.jpg', '2024-02-21 16:50:36', 'b.francia.516403@umindanao.edu.ph', '', 1, '', '0000-00-00 00:00:00'),
(30, 'tyson', '$2y$10$sfnoQyX9a29e1DX7oltcT..VQnaL9Zs6XZpF53rzps8Rri/b12ena', 'staff', '2702202405374913226688_836640433109192_6892683001309788498_n.jpg', '2024-02-27 04:37:53', 'arnoldcfrancia@gmail.com', '', 1, '', '0000-00-00 00:00:00'),
(32, 'britz', '$2y$10$P3b2opa88peMlakXRj09C.QcWpwThFP8xOY8JFGTcB6uLwGEuoUGq', 'staff', '040320241005521560527_496060257167213_1092531032_n.jpg', '2024-03-04 09:05:55', 'franciabritz17@gmail.com', '', 1, '', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblmedi_mat`
--
ALTER TABLE `tblmedi_mat`
  ADD PRIMARY KEY (`medID`);

--
-- Indexes for table `tbloperation`
--
ALTER TABLE `tbloperation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paymentID` (`paymentID`);

--
-- Indexes for table `tblowner`
--
ALTER TABLE `tblowner`
  ADD PRIMARY KEY (`OwnerID`);

--
-- Indexes for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petOwner` (`petOwner`);

--
-- Indexes for table `tblpet`
--
ALTER TABLE `tblpet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `OwnerID` (`OwnerID`);

--
-- Indexes for table `tblrecordservice`
--
ALTER TABLE `tblrecordservice`
  ADD PRIMARY KEY (`recordID`),
  ADD KEY `OwnerID` (`OwnerID`),
  ADD KEY `petID` (`petID`);

--
-- Indexes for table `tblservices`
--
ALTER TABLE `tblservices`
  ADD PRIMARY KEY (`serviceID`);

--
-- Indexes for table `tbltreatmentplan`
--
ALTER TABLE `tbltreatmentplan`
  ADD PRIMARY KEY (`treatID`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tblmedi_mat`
--
ALTER TABLE `tblmedi_mat`
  MODIFY `medID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbloperation`
--
ALTER TABLE `tbloperation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `tblowner`
--
ALTER TABLE `tblowner`
  MODIFY `OwnerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `tblpayments`
--
ALTER TABLE `tblpayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `tblpet`
--
ALTER TABLE `tblpet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `tblrecordservice`
--
ALTER TABLE `tblrecordservice`
  MODIFY `recordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `tblservices`
--
ALTER TABLE `tblservices`
  MODIFY `serviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbltreatmentplan`
--
ALTER TABLE `tbltreatmentplan`
  MODIFY `treatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`);

--
-- Constraints for table `tbloperation`
--
ALTER TABLE `tbloperation`
  ADD CONSTRAINT `tbloperation_ibfk_1` FOREIGN KEY (`paymentID`) REFERENCES `tblpayments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD CONSTRAINT `tblpayments_ibfk_1` FOREIGN KEY (`petOwner`) REFERENCES `tblowner` (`OwnerID`) ON UPDATE CASCADE;

--
-- Constraints for table `tblrecordservice`
--
ALTER TABLE `tblrecordservice`
  ADD CONSTRAINT `tblrecordservice_ibfk_1` FOREIGN KEY (`OwnerID`) REFERENCES `tblowner` (`OwnerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tblrecordservice_ibfk_2` FOREIGN KEY (`petID`) REFERENCES `tblpet` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
