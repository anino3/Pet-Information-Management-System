# ABMS : MySQL database backup
#
# Generated: Wednesday 14. February 2024
# Hostname: localhost
# Database: petshop
# --------------------------------------------------------


#
# Delete any existing table `tbl_users`
#

DROP TABLE IF EXISTS `tbl_users`;


#
# Table structure of table `tbl_users`
#



CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_users VALUES("10","staff","6ccb4b7c39a6e77f76ecfa935a855c6c46ad5611","staff","03052021043218icon.png","2021-05-03 10:32:18");
INSERT INTO tbl_users VALUES("11","admin","d033e22ae348aeb5660fc2140aec35850c4da997","administrator","18062023163343123d.jpg","2021-05-03 10:33:03");
INSERT INTO tbl_users VALUES("12","britz","0df65b824fe14cfea4c4dc9b1331714ada0eb397","staff","","2024-02-14 00:19:32");



#
# Delete any existing table `tblblotter`
#

DROP TABLE IF EXISTS `tblblotter`;


#
# Table structure of table `tblblotter`
#



CREATE TABLE `tblblotter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `complainant` varchar(100) DEFAULT NULL,
  `respondent` varchar(100) DEFAULT NULL,
  `victim` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `details` varchar(10000) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




#
# Delete any existing table `tblbrgy_info`
#

DROP TABLE IF EXISTS `tblbrgy_info`;


#
# Table structure of table `tblbrgy_info`
#



CREATE TABLE `tblbrgy_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `brgy_name` varchar(50) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `city_logo` varchar(100) DEFAULT NULL,
  `brgy_logo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblbrgy_info VALUES("1","Samar1","POBLACION KALIBO","BRGY KALIBO AKLAN","0919-1234567","Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Sed porttitor lectus nibh. Curabitur aliquet quam id dui posuere blandit.","09052021075440182970012_615550183178722_2776607156578360582_n.jpg","03052021033434brgy-logo.png","0905202107542630042021035316lgu-logo.png");



#
# Delete any existing table `tblchairmanship`
#

DROP TABLE IF EXISTS `tblchairmanship`;


#
# Table structure of table `tblchairmanship`
#



CREATE TABLE `tblchairmanship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblchairmanship VALUES("2","Presiding Officer");
INSERT INTO tblchairmanship VALUES("3","Committee on Appropriation");
INSERT INTO tblchairmanship VALUES("4","Committee on Peace & Order");
INSERT INTO tblchairmanship VALUES("5","Committee on Health");
INSERT INTO tblchairmanship VALUES("6","Committee on Education");
INSERT INTO tblchairmanship VALUES("7","Committee on Rules");
INSERT INTO tblchairmanship VALUES("8","Committee on Infra");
INSERT INTO tblchairmanship VALUES("9","Committee on Solid Waste");
INSERT INTO tblchairmanship VALUES("10","Committee on Sports");
INSERT INTO tblchairmanship VALUES("11","No Chairmanship");



#
# Delete any existing table `tblmedi_mat`
#

DROP TABLE IF EXISTS `tblmedi_mat`;


#
# Table structure of table `tblmedi_mat`
#



CREATE TABLE `tblmedi_mat` (
  `medID` int(11) NOT NULL AUTO_INCREMENT,
  `itemCode` int(11) NOT NULL,
  `itemName` varchar(100) NOT NULL,
  `typeName` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expirationDate` date NOT NULL,
  `itemPrice` int(11) NOT NULL,
  `itemImage` longblob DEFAULT NULL,
  PRIMARY KEY (`medID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblmedi_mat VALUES("15","1311","Distilled Water","Hydration","11","2030-12-28","20","14022024051501p2.jpg");



#
# Delete any existing table `tblofficials`
#

DROP TABLE IF EXISTS `tblofficials`;


#
# Table structure of table `tblofficials`
#



CREATE TABLE `tblofficials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `chairmanship` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `termstart` date DEFAULT NULL,
  `termend` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblofficials VALUES("1","Peter Guevarra	","2","4","2021-04-29","2021-05-01","Active");
INSERT INTO tblofficials VALUES("4","Marlon A. Lorio","3","7","2021-04-03","2021-04-24","Active");
INSERT INTO tblofficials VALUES("5","GARRY A. RAFEL","4","8","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("6","TRILLION LOWRY	","5","9","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("7","MELANIE M. ELBOR	","6","10","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("8","ERLINDA V. VITUS	","7","11","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("9","JOEDAVINCE","8","12","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("10","ALEJANDRO A. CAGAMPANG	","9","13","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("11","JOSEPH P. PARDOS	","10","14","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("12","RUTH A. BACAG	","11","15","2021-04-03","2021-04-03","Active");
INSERT INTO tblofficials VALUES("13","DIANNE A. CURRY	","11","16","2021-04-03","2021-04-03","Active");



#
# Delete any existing table `tbloperation`
#

DROP TABLE IF EXISTS `tbloperation`;


#
# Table structure of table `tbloperation`
#



CREATE TABLE `tbloperation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `petName` varchar(100) NOT NULL,
  `petOwner` varchar(100) NOT NULL,
  `operationType` varchar(100) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL,
  `details` varchar(10000) NOT NULL,
  `status` varchar(50) NOT NULL,
  `finishDate` date DEFAULT NULL,
  `finishTime` time DEFAULT NULL,
  `finishDetails` text DEFAULT NULL,
  `operationCost` int(11) NOT NULL,
  `operationID` int(11) NOT NULL,
  `medimat_used` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




#
# Delete any existing table `tblowner`
#

DROP TABLE IF EXISTS `tblowner`;


#
# Table structure of table `tblowner`
#



CREATE TABLE `tblowner` (
  `OwnerID` int(11) NOT NULL AUTO_INCREMENT,
  `OwnerName` varchar(255) NOT NULL,
  `OwnerAddress` varchar(255) NOT NULL,
  `OwnerCity` varchar(255) NOT NULL,
  `OwnerZip` varchar(10) NOT NULL,
  `OwnerMobileNo` varchar(15) NOT NULL,
  `OwnerEmail` varchar(255) NOT NULL,
  `balance` int(11) NOT NULL,
  PRIMARY KEY (`OwnerID`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblowner VALUES("107","James Reid","86 Rizal St.","86 Rizal St.","8000","09204011019","franciabritz17@gmail.com","0");
INSERT INTO tblowner VALUES("109","Katrine Bernardo","Quezon Boulevard","Manila","8000","09163619215","kbernardo@gmail.com","0");



#
# Delete any existing table `tblpayments`
#

DROP TABLE IF EXISTS `tblpayments`;


#
# Table structure of table `tblpayments`
#



CREATE TABLE `tblpayments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `details` varchar(100) DEFAULT NULL,
  `amounts` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `petOwner` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `petOwner` (`petOwner`),
  CONSTRAINT `tblpayments_ibfk_1` FOREIGN KEY (`petOwner`) REFERENCES `tblowner` (`OwnerID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblpayments VALUES("12","'Pet Groom and Wash'","2000.00","2024-02-11","admin"," James Reid","107");
INSERT INTO tblpayments VALUES("20","Vaccinations","2500.00","2024-02-12","admin"," James Reid","107");
INSERT INTO tblpayments VALUES("21","Dental Care, Microchipping","2000.00","2024-02-12","admin"," James Reid","107");
INSERT INTO tblpayments VALUES("22","Microchipping","1500.00","2024-02-13","admin"," Katrine Bernardo","109");



#
# Delete any existing table `tblpermit`
#

DROP TABLE IF EXISTS `tblpermit`;


#
# Table structure of table `tblpermit`
#



CREATE TABLE `tblpermit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `owner1` varchar(200) DEFAULT NULL,
  `owner2` varchar(80) DEFAULT NULL,
  `nature` varchar(220) DEFAULT NULL,
  `applied` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblpermit VALUES("4","SH Food Group 1","SH Food Group 1","SH Food Group 2","SH Food Group 1","2021-04-30");
INSERT INTO tblpermit VALUES("5","Atrium Salon & Studio","SH Food Group 213","","Atrium Salon & Studio","2021-04-30");
INSERT INTO tblpermit VALUES("6","qweqwe","qweqwe","qweqwe","qweqwe","2024-02-06");
INSERT INTO tblpermit VALUES("7","qweqwe","qweqwe","qweqwe","qweqwe","2024-02-06");



#
# Delete any existing table `tblpet`
#

DROP TABLE IF EXISTS `tblpet`;


#
# Table structure of table `tblpet`
#



CREATE TABLE `tblpet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `national_id` varchar(255) NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `pet_type` varchar(255) NOT NULL,
  `pet_breed` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `pet_notes` varchar(255) NOT NULL,
  `picture` longblob DEFAULT NULL,
  `OwnerID` int(11) NOT NULL,
  `isActive` text NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`id`),
  KEY `OwnerID` (`OwnerID`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblpet VALUES("108","514912","ChongChong","Dog","datchun","2024-02-07","12","Male","Brown Furr","01022024124615418321486_382890077622969_453791233018291682_n.jpg","107","No");
INSERT INTO tblpet VALUES("110","286140","Beast","Cat","Permasion","2024-02-13","1","Asexual","qweqweqwe","13022024023928download.jpg","109","Yes");
INSERT INTO tblpet VALUES("111","839979","Sweet","Dog","Shitzu","2024-02-13","12","Female","VERY CUTE","130220240253291044547_390414281065145_147308189_n.jpg","109","Yes");



#
# Delete any existing table `tblposition`
#

DROP TABLE IF EXISTS `tblposition`;


#
# Table structure of table `tblposition`
#



CREATE TABLE `tblposition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(50) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblposition VALUES("4","Captain","1");
INSERT INTO tblposition VALUES("7","Councilor 1","2");
INSERT INTO tblposition VALUES("8","Councilor 2","3");
INSERT INTO tblposition VALUES("9","Councilor 3","4");
INSERT INTO tblposition VALUES("10","Councilor 4","5");
INSERT INTO tblposition VALUES("11","Councilor 5","6");
INSERT INTO tblposition VALUES("12","Councilor 6","7");
INSERT INTO tblposition VALUES("13","Councilor 7","8");
INSERT INTO tblposition VALUES("14","SK Chairman","9");
INSERT INTO tblposition VALUES("15","Secretary","10");
INSERT INTO tblposition VALUES("16","Treasurer","11");



#
# Delete any existing table `tblprecinct`
#

DROP TABLE IF EXISTS `tblprecinct`;


#
# Table structure of table `tblprecinct`
#



CREATE TABLE `tblprecinct` (
  `id` int(11) NOT NULL,
  `precinct` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




#
# Delete any existing table `tblpurok`
#

DROP TABLE IF EXISTS `tblpurok`;


#
# Table structure of table `tblpurok`
#



CREATE TABLE `tblpurok` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblpurok VALUES("1","Purok 1","Tikang Kanda Babon ngadto liwat kanda Babon");
INSERT INTO tblpurok VALUES("2","Purok 2","Amon ngadto ira");
INSERT INTO tblpurok VALUES("3","Purok 3","afawewqeqweqweqw");
INSERT INTO tblpurok VALUES("4","Purok 4","dsfdsf");
INSERT INTO tblpurok VALUES("5","Purok 5","rewrew");
INSERT INTO tblpurok VALUES("6","Purok 6","rewrewr");
INSERT INTO tblpurok VALUES("7","Purok 7","rew");
INSERT INTO tblpurok VALUES("8","Purok 7","rew");



#
# Delete any existing table `tblrecordservice`
#

DROP TABLE IF EXISTS `tblrecordservice`;


#
# Table structure of table `tblrecordservice`
#



CREATE TABLE `tblrecordservice` (
  `recordID` int(11) NOT NULL AUTO_INCREMENT,
  `petOwner` text NOT NULL,
  `petNames` text NOT NULL,
  `serviceTypes` text NOT NULL,
  `date` date NOT NULL,
  `totalCost` int(11) NOT NULL,
  `petID` int(11) NOT NULL,
  `OwnerID` int(11) NOT NULL,
  `paid` varchar(10) NOT NULL,
  PRIMARY KEY (`recordID`),
  KEY `OwnerID` (`OwnerID`),
  KEY `petID` (`petID`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblrecordservice VALUES("53","Katrine Bernardo","Beast","Microchipping","2024-02-13","1500","110","109","true");
INSERT INTO tblrecordservice VALUES("55","James Reid","ChingChing","Pest & Parasite Prevention","2024-02-14","1500","107","107","0");
INSERT INTO tblrecordservice VALUES("56","Katrine Bernardo","Sweet","Dermatology, Pet Groom and Wash","2024-02-14","2700","111","109","0");



#
# Delete any existing table `tblservices`
#

DROP TABLE IF EXISTS `tblservices`;


#
# Table structure of table `tblservices`
#



CREATE TABLE `tblservices` (
  `serviceID` int(11) NOT NULL AUTO_INCREMENT,
  `serviceName` text NOT NULL,
  `serviceDescription` text NOT NULL,
  `servicePrice` int(11) NOT NULL,
  PRIMARY KEY (`serviceID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tblservices VALUES("1","Wellness and sick exams","Our vets examine your pet from nose to tail—evaluating their overall health and recommending preventive care, diagnostics and treatments.","3000");
INSERT INTO tblservices VALUES("2","Vaccinations","Vaccinations are recommended for all dogs, cats and ferrets to help protect your pet from deadly infectious diseases.","2500");
INSERT INTO tblservices VALUES("3","Pest & Parasite Prevention","Help protect your pet from fleas, ticks, heartworms and other parasites year-round so they avoid deadly diseases.

","1500");
INSERT INTO tblservices VALUES("4","Dental Care","Complete dental care diagnostics—including digital dental X-rays, anesthetized cleaning.","500");
INSERT INTO tblservices VALUES("5","Microchipping","A microchip could be the difference between your pet being lost and found.","1500");
INSERT INTO tblservices VALUES("6","Dermatology","Itchy skin and ears, allergies, infections and parasites are some of the most common health issues pets face that we can help treat.","1500");
INSERT INTO tblservices VALUES("7","Pet Groom and Wash","Pet grooming will involve shaving, clipping, or trimming, and wash.","1200");

