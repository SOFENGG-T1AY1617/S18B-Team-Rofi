DROP SCHEMA IF EXISTS `reservation_system`;
CREATE SCHEMA `reservation_system`;

CREATE TABLE `reservation_system`.`departments` (
  `departmentid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`departmentid`));

CREATE TABLE `reservation_system`.`area_types` (
  `area_typeid` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`area_typeid`));

CREATE TABLE `reservation_system`.`buildings` (
  `buildingid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `area_typeid` INT NOT NULL,
  PRIMARY KEY (`buildingid`),
  INDEX `area_typeidx` (`area_typeid` ASC),
  CONSTRAINT `area_typeid`
    FOREIGN KEY (`area_typeid`)
    REFERENCES `reservation_system`.`area_types` (`area_typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`rooms` (
  `roomid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `buildingid` INT NOT NULL,
  `departmentid` INT NOT NULL,
  PRIMARY KEY (`roomid`),
  INDEX `buildingid_idx` (`buildingid` ASC),
  INDEX `department_idx` (`departmentid` ASC),
  CONSTRAINT `buildingid`
    FOREIGN KEY (`buildingid`)
    REFERENCES `reservation_system`.`buildings` (`buildingid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `departmentid`
    FOREIGN KEY (`departmentid`)
    REFERENCES `reservation_system`.`departments` (`departmentid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`computers` (
  `computerid` INT NOT NULL AUTO_INCREMENT,
  `computerno` INT NOT NULL,
  `roomid` INT NOT NULL,
  PRIMARY KEY (`computerid`),
  INDEX `roomid_idx` (`roomid` ASC),
  CONSTRAINT `roomid`
    FOREIGN KEY (`roomid`)
    REFERENCES `reservation_system`.`rooms` (`roomid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`colleges` (
  `collegeid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`collegeid`));

CREATE TABLE `reservation_system`.`types` (
  `typeid` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`typeid`));


CREATE TABLE `reservation_system`.`reservations` (
  `reservationid` INT NOT NULL AUTO_INCREMENT,
  `computerid` INT NOT NULL,
  `useridno` INT NOT NULL,
  `email` VARCHAR(30) NOT NULL,
  `date` DATE NOT NULL,
  `start_restime` TIME NOT NULL,
  `end_restime` TIME NOT NULL,
  `collegeid` INT,
  `typeid` INT NOT NULL,
  `verified` BIT NOT NULL DEFAULT 0,
  `verificationcode` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`reservationid`),
  INDEX `typeid_idx` (`typeid` ASC),
  INDEX `collegeid_idx` (`collegeid` ASC),
  INDEX `computerid_idx` (`computerid` ASC),
  CONSTRAINT `computerid`
    FOREIGN KEY (`computerid`)
    REFERENCES `reservation_system`.`computers` (`computerid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `typeid`
    FOREIGN KEY (`typeid`)
    REFERENCES `reservation_system`.`types` (`typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `collegeid`
    FOREIGN KEY (`collegeid`)
    REFERENCES `reservation_system`.`colleges` (`collegeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`admin_types` (
  `admin_typeid` INT NOT NULL AUTO_INCREMENT,
  `admin_type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`admin_typeid`));

CREATE TABLE `reservation_system`.`administrators` (
  `administratorid` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `admin_departmentid` INT NOT NULL,
  `admin_typeid` INT NOT NULL,
  PRIMARY KEY (`administratorid`),
  INDEX `admin_departmentid_idx` (`admin_departmentid` ASC),
  INDEX `admin_typeid_idx` (`admin_typeid` ASC),
  CONSTRAINT `admin_departmentid`
	FOREIGN KEY (`admin_departmentid`)
	REFERENCES `reservation_system`.`departments` (`departmentid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION,
  CONSTRAINT `admin_typeid`
	FOREIGN KEY (`admin_typeid`)
	REFERENCES `reservation_system`.`admin_types` (`admin_typeid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`moderators` (
  `moderatorid` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `mod_departmentid` INT NOT NULL,
  PRIMARY KEY (`moderatorid`),
  INDEX `mod_departmentid_idx` (`mod_departmentid` ASC),
	CONSTRAINT `mod_departmentid`
	FOREIGN KEY (`mod_departmentid`)
	REFERENCES `reservation_system`.`departments` (`departmentid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`business_rules` (
  `business_rulesid` INT NOT NULL AUTO_INCREMENT,
  `departmentid` INT NOT NULL,
  `interval` INT NOT NULL,
  `limit` INT NOT NULL,
  `accessibility` INT NOT NULL,
  `reservation_expiry` INT NOT NULL,
  `confirmation_expiry` INT NOT NULL,
  PRIMARY KEY (`business_rulesid`),
  INDEX `department_id_idx` (`departmentid` ASC),
  CONSTRAINT `business_departmentid`
    FOREIGN KEY (`departmentid`)
    REFERENCES `reservation_system`.`departments` (`departmentid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`email_extension`(
  `email_extensionid` INT NOT NULL AUTO_INCREMENT,
  `email_extension` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`email_extensionid`));

/*dummy data*/
INSERT INTO `reservation_system`.`email_extension`(`email_extension`)
VALUES ("@dlsu.edu.ph"),
	   ("@delasalle.ph");

INSERT INTO `reservation_system`.`area_types` (`type`)
VALUES ("Room"),
	   ("Floor");

INSERT INTO `reservation_system`.`buildings` (`name`, `area_typeid`)
VALUES ("Gokongwei Hall", 1),
	   ("Henry Sy Sr. Hall", 2),
	   ("Miguel Hall", 1),
	   ("Saint Joseph Hall", 1);

INSERT INTO `reservation_system`.`departments` (`name`)
VALUES ("ITS"),
	   ("Library");

INSERT INTO `reservation_system`.`rooms` (`name`, `buildingid`, `departmentid`)
VALUES ("G202A", 1, 1),
	   ("G202B", 1, 1),
	   ("SJ212", 4, 1),
	   ("6F", 2, 2),
	   ("7F", 2, 2),
	   ("8F", 2, 2);

INSERT INTO `reservation_system`.`computers` (`computerno`, `roomid`)
VALUES (1, 1),
	   (2, 1),
	   (3, 1),
	   (4, 1),
	   (5, 1),
	   (6, 1),
	   (7, 1),
	   (8, 1),
	   (9, 1),
	   (10, 1),
	   (1, 2),
	   (2, 2),
	   (3, 2),
	   (4, 2),
	   (5, 2),
	   (6, 2),
	   (7, 2),
	   (8, 2),
	   (9, 2),
	   (10, 2),
	   (1, 3),
	   (2, 3),
	   (3, 3),
	   (4, 3),
	   (5, 3),
	   (6, 3),
	   (7, 3),
	   (8, 3),
	   (9, 3),
	   (10, 3),
	   (1, 4),
	   (2, 4),
	   (3, 4),
	   (4, 4),
	   (5, 4),
	   (6, 4),
	   (7, 4),
	   (8, 4),
	   (9, 4),
	   (10, 4),
	   (1, 5),
	   (2, 5),
	   (3, 5),
	   (4, 5),
	   (5, 5),
	   (6, 5),
	   (7, 5),
	   (8, 5),
	   (9, 5),
	   (10, 5),
	   (1, 6),
	   (2, 6),
	   (3, 6),
	   (4, 6),
	   (5, 6),
	   (6, 6),
	   (7, 6),
	   (8, 6),
	   (9, 6),
	   (10, 6);

INSERT INTO `reservation_system`.`colleges` (`name`)
VALUES ("Ramon V. Del Rosario College of Business"),
	   ("College of Computer Studies"),
	   ("Gokongwei College of Engineering"),
	   ("School of Economics"),
	   ("Br. Andrew Gonzales College of Education"),
	   ("College of Law"),
	   ("College of Liberal Arts"),
	   ("College of Science");

INSERT INTO `reservation_system`.`types` (`type`)
VALUES ("Senior High School"),
	   ("Undergraduate"),
	   ("Graduate"),
	   ("Faculty"),
	   ("Staff");
       
INSERT INTO `reservation_system`.`reservations`
	(`computerid`, `useridno`, `email`, `date`, `start_restime`, `end_restime`,
    `collegeid`, `typeid`, `verificationcode`)
VALUES (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-18", "11:00:00", "11:14:59", 2, 2, "45t45y0965134213yktreioet54j209"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-18", "11:15:00", "11:29:59", 2, 2, "45t45y0965134213yktreioet54j209"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-19", "11:00:00", "11:14:59", 2, 2, "45t45y0965134213yktreioet54j210"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-19", "11:15:00", "11:29:59", 2, 2, "45t45y0965134213yktreioet54j210"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-20", "11:00:00", "11:14:59", 2, 2, "45t45y0965134213yktreioet54j211"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-20", "11:15:00", "11:29:59", 2, 2, "45t45y0965134213yktreioet54j211"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-11-21", "11:00:00", "11:14:59", 2, 2, "45t45y0965134213yktreioet54j212"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-11-21", "11:15:00", "11:29:59", 2, 2, "45t45y0965134213yktreioet54j212");

INSERT INTO `reservation_system`.`admin_types` (`admin_type`)
VALUES ("Super Administrator"),
	   ("Administrator");

INSERT INTO `reservation_system`.`administrators` 
	(`email`, `last_name`, `first_name`, `admin_departmentid`, `admin_typeid`, `password`)
VALUES ("james.sy@dlsu.edu.ph","Sy", "James", 1, 1, "password"),
	   ("bing.dancalan@dlsu.edu.ph", "Dancalan", "Bing", 2, 2, "password"),
	   ("juan.delacruz@dlsu.edu.ph","Dela Cruz", "Juan", 1, 2, "password");

INSERT INTO `reservation_system`.`moderators` 
	(`email`, `last_name`, `first_name`, `mod_departmentid`, `password`)
VALUES ("rofi_santos@dlsu.edu.ph","Santos", "Rofi", 1, "password"),
	   ("patrich.tobias@dlsu.edu.ph", "Tobias", "Patrick", 1, "password"),
	   ("benson.polican","Polican", "Benson", 2, "password");

INSERT INTO `reservation_system`.`business_rules` 
	(`departmentid`, `interval`, `limit`, `accessibility`, `reservation_expiry`, `confirmation_expiry`)
VALUES (1, 15, 4, 1, 15, 60),
	   (2, 20, 5, 1, 20, 90);
