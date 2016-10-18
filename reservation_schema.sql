DROP SCHEMA IF EXISTS `reservation_system`;
CREATE SCHEMA `reservation_system`;

CREATE TABLE `reservation_system`.`buildings` (
  `buildingid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`buildingid`));

CREATE TABLE `reservation_system`.`rooms` (
  `roomid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `buildingid` INT NOT NULL,
  `department` VARCHAR(30) NOT NULL DEFAULT 'ITS',
  PRIMARY KEY (`roomid`),
  INDEX `buildingid_idx` (`buildingid` ASC),
  CONSTRAINT `buildingid`
    FOREIGN KEY (`buildingid`)
    REFERENCES `reservation_system`.`buildings` (`buildingid`)
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
  `start_resdatetime` DATETIME NOT NULL,
  `end_resdatetime` DATETIME NOT NULL,
  `collegeid` INT NOT NULL,
  `typeid` INT NOT NULL,
  `verified` BIT NOT NULL DEFAULT 0,
  `verificationcode` VARCHAR(32) NOT NULL,
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



/*dummy data*/
INSERT INTO `reservation_system`.`buildings` (`name`)
VALUES ("Gokongwei Hall"),
	   ("Henry Sy Sr. Hall"),
	   ("Miguel Hall"),
	   ("Saint Joseph Hall");

INSERT INTO `reservation_system`.`rooms` (`name`, `buildingid`)
VALUES ("G202A", 1),
	   ("G202B", 1);

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
	   (10, 2);

INSERT INTO `reservation_system`.`colleges` (`name`)
VALUES ("College of Business"),
	   ("College of Computer Science"),
	   ("College of Engineering"),
	   ("School of Economics"),
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
	(`computerid`, `useridno`, `email`, `start_resdatetime`, `end_resdatetime`,
    `collegeid`, `typeid`, `verificationcode`)
VALUES (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-18 11:00:00", "2016-10-18 11:14:59", 2, 2, "45t45y0965yktreioet54j209"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-18 11:15:00", "2016-10-18 11:29:59", 2, 2, "45t45y0965yktreioet54j209"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-19 11:00:00", "2016-10-19 11:14:59", 2, 2, "45t45y0965yktreioet54j210"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-19 11:15:00", "2016-10-19 11:29:59", 2, 2, "45t45y0965yktreioet54j210"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-20 11:00:00", "2016-10-20 11:14:59", 2, 2, "45t45y0965yktreioet54j211"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-20 11:15:00", "2016-10-20 11:29:59", 2, 2, "45t45y0965yktreioet54j211"),
       (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-21 11:00:00", "2016-10-21 11:14:59", 2, 2, "45t45y0965yktreioet54j212"),
	   (1, 11425520, "kevin_gray_chan@dlsu.edu.ph", "2016-10-21 11:15:00", "2016-10-21 11:29:59", 2, 2, "45t45y0965yktreioet54j212");