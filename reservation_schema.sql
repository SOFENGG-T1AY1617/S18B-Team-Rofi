DROP SCHEMA IF EXISTS `reservation_system`;
CREATE SCHEMA `reservation_system`;

CREATE TABLE `reservation_system`.`rooms` (
  `roomid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `building` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`roomid`));

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
  `collegeid` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`collegeid`));

CREATE TABLE `reservation_system`.`types` (
  `typeid` INT NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`typeid`));


CREATE TABLE `reservation_system`.`reservations` (
  `reservationid` INT NOT NULL AUTO_INCREMENT,
  `computerid` INT NOT NULL,
  `useridno` INT NOT NULL,
  `reservedatetime` DATETIME NOT NULL,
  `collegeid` INT NOT NULL,
  `typeid` INT NOT NULL,
  `verified` BIT NOT NULL,
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

INSERT INTO `reservation_system`.`rooms` (`name`, `building`)
VALUES ("G202A", "Gokongwei"),
	   ("G202B", "Gokongwei");

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

INSERT INTO `reservation_system`.`types` (`name`)
VALUES ("Senior High School"),
	   ("Undergraduate"),
	   ("Graduate"),
	   ("Faculty"),
	   ("Staff");