
DROP TABLE IF EXISTS `tbl_dosages`;
CREATE TABLE `tbl_dosages`
(
  `dosageId` int NOT NULL AUTO_INCREMENT,
  `medicineId` int NOT NULL,
  `userId` int NOT NULL,
  `dateTaken` varchar
(20) NOT NULL,
  `timeTaken` varchar
(10) NOT NULL,
  `dateInputted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`dosageId`),
  KEY `fk_dosage_medicine`
(`medicineId`),
  KEY `fk_dosage_user`
(`userId`),
  CONSTRAINT `fk_dosage_medicine` FOREIGN KEY
(`medicineId`) REFERENCES `tbl_medicine`
(`medicineId`) ON
DELETE CASCADE ON
UPDATE CASCADE,
  CONSTRAINT `fk_dosage_user` FOREIGN KEY
(`userId`) REFERENCES `tbl_users`
(`userId`) ON
DELETE CASCADE ON
UPDATE CASCADE
)



DROP TABLE IF EXISTS `tbl_medicine`;
CREATE TABLE `tbl_medicine`
(
  `medicineId` int NOT NULL AUTO_INCREMENT,
  `medicineName` varchar
(100) NOT NULL,
  `dosage` int NOT NULL,
  `dosageUnit` varchar
(10) NOT NULL,
  `milligrams` int NOT NULL,
  `milligramUnit` varchar
(10) NOT NULL,
  `frequency` int NOT NULL,
  `frequencyUnit` varchar
(25) NOT NULL,
  `userId` int NOT NULL,
  `dateInputted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`medicineId`),
  KEY `fk_medicine_user`
(`userId`),
  CONSTRAINT `fk_medicine_user` FOREIGN KEY
(`userId`) REFERENCES `tbl_users`
(`userId`) ON
DELETE CASCADE ON
UPDATE CASCADE
)



DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users`
(
  `userId` int NOT NULL AUTO_INCREMENT,
  `userFullname` varchar
(200) NOT NULL,
  `userName` varchar
(200) NOT NULL,
  `userEmail` varchar
(200) NOT NULL,
  `userPassword` varchar
(200) NOT NULL,
  `dateRegistered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`userId`)
) 



