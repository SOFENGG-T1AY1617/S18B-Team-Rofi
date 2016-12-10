DROP SCHEMA IF EXISTS `reservation_system`;
CREATE SCHEMA `reservation_system`;

CREATE TABLE `reservation_system`.`departments` (
  `departmentid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`departmentid`));

CREATE TABLE `reservation_system`.`computer_status` (
  `statusid` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`statusid`));

CREATE TABLE `reservation_system`.`attendance` (
  `attendanceid` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`attendanceid`));

CREATE TABLE `reservation_system`.`area_types` (
  `area_typeid` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(20) NOT NULL,
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
  `statusid` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`computerid`),
  INDEX `roomid_idx` (`roomid` ASC),
  INDEX `statusid_idx` (`statusid` ASC),
  CONSTRAINT `roomid`
    FOREIGN KEY (`roomid`)
    REFERENCES `reservation_system`.`rooms` (`roomid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `statusid`
    FOREIGN KEY (`statusid`)
    REFERENCES `reservation_system`.`computer_status` (`statusid`)
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

CREATE TABLE `reservation_system`.`users` (
  `userid` INT NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `first_name` VARCHAR(30) NOT NULL,
  `middle_name` VARCHAR(30) NOT NULL,
  `birthdate` DATE NOT NULL,
  `typeid` INT NOT NULL,
  `collegeid` INT NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`userid`),
  INDEX `typeid_idx` (`typeid` ASC),
  INDEX `collegeid_idx` (`collegeid` ASC),
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

CREATE TABLE `reservation_system`.`reservations` (
  `reservationid` INT NOT NULL AUTO_INCREMENT,
  `computerid` INT NOT NULL,
  `userid` INT NOT NULL,
  `date` DATE NOT NULL,
  `start_restime` TIME NOT NULL,
  `end_restime` TIME NOT NULL,
  `verified` BIT NOT NULL DEFAULT 0,
  `verificationcode` VARCHAR(40) NOT NULL,
  `attendance` INT NOT NULL DEFAULT 0,
  `time_reserved` DATETIME DEFAULT NULL,
  PRIMARY KEY (`reservationid`),
  INDEX `computerid_idx` (`computerid` ASC),
  CONSTRAINT `computerid`
    FOREIGN KEY (`computerid`)
    REFERENCES `reservation_system`.`computers` (`computerid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

DELIMITER $$
DROP TRIGGER IF EXISTS reservation_system.reservations_BINS$$
USE `reservation_system`$$
CREATE TRIGGER `reservations_BINS` BEFORE INSERT ON `reservations` FOR EACH ROW
BEGIN
  IF (NEW.time_reserved IS NULL) THEN
  SET NEW.time_reserved = CONCAT(curdate(), ' ', curtime());
  END IF;
END$$
DELIMITER ;

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
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
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

CREATE TABLE `reservation_system`.`archive_reservations` (
  `archive_reservationid` INT NOT NULL AUTO_INCREMENT,
  `computerno` INT NOT NULL,
  `room_name` VARCHAR(100) NOT NULL,
  `userid` INT NOT NULL,
  `date` DATE NOT NULL,
  `start_restime` TIME NOT NULL,
  `end_restime` TIME NOT NULL,
  `verified` BIT NOT NULL DEFAULT 0,
  `verificationcode` VARCHAR(40) NOT NULL,
  `attendance` INT NOT NULL DEFAULT 0, 
  PRIMARY KEY (`archive_reservationid`));

CREATE TABLE `reservation_system`.`tag_mod_rooms` (
  `tag_mod_roomsid` INT NOT NULL AUTO_INCREMENT,
  `moderatorid` INT NOT NULL,
  `roomid` INT NOT NULL,
  PRIMARY KEY (`tag_mod_roomsid`),
  INDEX `moderatorid_idx` (`moderatorid` ASC),
  INDEX `roomid_idx` (`roomid` ASC),
  CONSTRAINT `mod_rooms_moderatorid`
	FOREIGN KEY (`moderatorid`)
	REFERENCES `reservation_system`.`moderators` (`moderatorid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION,
  CONSTRAINT `mod_rooms_roomid`
	FOREIGN KEY (`roomid`)
	REFERENCES `reservation_system`.`rooms` (`roomid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION);

CREATE TABLE `reservation_system`.`closed_pcs` (
  `closed_pcsid` INT NOT NULL AUTO_INCREMENT,
  `computerid` INT NOT NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`closed_pcsid`),
  INDEX `computerid_idx` (`computerid` ASC),
  CONSTRAINT `closed_computerid`
	FOREIGN KEY (`computerid`)
	REFERENCES `reservation_system`.`computers` (`computerid`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION);

/*dummy data*/
INSERT INTO `reservation_system`.`email_extension`(`email_extension`)
VALUES ("dlsu.edu.ph"),
	   ("delasalle.ph");

INSERT INTO `reservation_system`.`area_types` (`type`)
VALUES ("Room"),
	   ("Floor");

INSERT INTO `reservation_system`.`computer_status` (`status`)
VALUES ("Enabled"),
	   ("Disabled"),
       ("Taken");

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

INSERT INTO `reservation_system`.`computers` (`computerno`, `roomid`, `statusid`)
VALUES (1, 1, 1),
	   (2, 1, 2),
	   (3, 1, 1),
	   (4, 1, 2),
	   (5, 1, 1),
	   (6, 1, 2),
	   (7, 1, 1),
	   (8, 1, 2),
	   (9, 1, 1),
	   (10, 1, 2),
	   (1, 2, 1),
	   (2, 2, 2),
	   (3, 2, 1),
	   (4, 2, 2),
	   (5, 2, 1),
	   (6, 2, 2),
	   (7, 2, 1),
	   (8, 2, 2),
	   (9, 2, 1),
	   (10, 2, 2),
	   (1, 3, 1),
	   (2, 3, 2),
	   (3, 3, 1),
	   (4, 3, 2),
	   (5, 3, 1),
	   (6, 3, 2),
	   (7, 3, 1),
	   (8, 3, 2),
	   (9, 3, 1),
	   (10, 3, 2),
	   (1, 4, 1),
	   (2, 4, 2),
	   (3, 4, 1),
	   (4, 4, 2),
	   (5, 4, 1),
	   (6, 4, 2),
	   (7, 4, 1),
	   (8, 4, 2),
	   (9, 4, 1),
	   (10, 4, 2),
	   (1, 5, 1),
	   (2, 5, 2),
	   (3, 5, 1),
	   (4, 5, 2),
	   (5, 5, 1),
	   (6, 5, 2),
	   (7, 5, 1),
	   (8, 5, 2),
	   (9, 5, 1),
	   (10, 5, 2),
	   (1, 6, 1),
	   (2, 6, 2),
	   (3, 6, 1),
	   (4, 6, 2),
	   (5, 6, 1),
	   (6, 6, 2),
	   (7, 6, 1),
	   (8, 6, 2),
	   (9, 6, 1),
	   (10, 6, 2);

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

INSERT INTO `reservation_system`.`users` (`userid`, `last_name`, `first_name`, `middle_name`, `birthdate`,
	`typeid`, `collegeid`, `email`, `password`)
VALUES(11428260, "Santos", "Rofi Emmanuelle", "Lectura", "1997-04-19", 2, 2, "rofi_santos@dlsu.edu.ph", "password"),
  (11425520, "Chan", "Kevin Gray", "Dayao", "1998-01-11", 2, 2, "kevin_gray_chan@dlsu.edu.ph", "password");
       
INSERT INTO `reservation_system`.`reservations`
	(`computerid`,  `date`, `userid`, `start_restime`, `end_restime`, `verificationcode`, `attendance`)
VALUES (1, "2016-10-18", 11428260, "11:00:00", "11:14:59", "45t45y0965134213yktreioet54j209", 1),
	   (1, "2016-10-18", 11428260, "11:15:00", "11:29:59", "45t45y0965134213yktreioet54j209", 1),
       (1, "2016-10-19", 11428260, "11:00:00", "11:14:59", "45t45y0965134213yktreioet54j210", 1),
	   (1, "2016-10-19", 11428260, "11:15:00", "11:29:59", "45t45y0965134213yktreioet54j210", 1),
       (1, "2016-10-20", 11428260, "11:00:00", "11:14:59", "45t45y0965134213yktreioet54j211", 1),
	   (1, "2016-10-20", 11428260, "11:15:00", "11:29:59", "45t45y0965134213yktreioet54j211", 1),
       (1, "2016-11-21", 11428260, "11:00:00", "11:14:59", "45t45y0965134213yktreioet54j212", 1),
	   (1, "2016-11-21", 11428260, "11:15:00", "11:29:59", "45t45y0965134213yktreioet54j212", 1),
	   (11, "2016-11-21", 11428260, "11:00:00", "11:14:59", "45t45y0965134213yktreioet54j212", 1),
	   (11, "2016-11-21", 11428260, "11:15:00", "11:29:59", "45t45y0965134213yktreioet54j212", 1);


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
	   ("patrick.tobias@dlsu.edu.ph", "Tobias", "Patrick", 1, "password"),
	   ("benson.polican@dlsu.edu.ph","Polican", "Benson", 2, "password");

INSERT INTO `reservation_system`.`business_rules` 
	(`departmentid`, `interval`, `limit`, `start_time`, `end_time`, `accessibility`, `reservation_expiry`, `confirmation_expiry`)
VALUES (1, 15, 4, "6:00:00", "20:00:00", 1, 15, 60),
	   (2, 20, 5, "8:00:00", "18:00:00", 1, 20, 90);

INSERT INTO `reservation_system`.`tag_mod_rooms` (`moderatorid`,`roomid`)
VALUES (1,1),
	   (2,2),
	   (3,4);

INSERT INTO `reservation_system`.`closed_pcs` (`computerid`, `start_datetime`, `end_datetime`)
VALUES (1, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (2, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (3, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (11, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (12, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (13, "2016-12-10 12:00:00", "2016-12-10 13:00:00"),
	   (21, "2016-12-10 12:00:00", "2016-12-10 13:00:00");


/*STORED PROCEDURES*/

USE `reservation_system`;
DROP procedure IF EXISTS `archive_past_reservations`;
DROP procedure IF EXISTS `archive_unconfirmed_reservations`;
DROP procedure IF EXISTS `archive_deleted_reservations_by_room`;
DROP procedure IF EXISTS `archive_deleted_reservations_by_computer`;

DELIMITER $$
USE `reservation_system`$$
-- date format => "YYYY-MM-DD" with quotations
-- time format => "HH:MM:SS" with quotations
CREATE PROCEDURE `archive_past_reservations` (IN curdate DATE, IN curtime TIME)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerno`, `room_name`, `userid`, `date`, `start_restime`, 
	`end_restime`, `verificationcode`, `attendance`)
	SELECT computer.`computerno`, room.`name`,`userid`, `date`, `start_restime`, 
		   `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations` reservation, 
		 `reservation_system`.`rooms` room, `reservation_system`.`computers` computer
	WHERE (curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`))
		  AND reservation.computerid = computer.computerid AND room.roomid = computer.roomid;
    DELETE FROM `reservation_system`.`reservations`
    WHERE curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`);
END$$


DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `archive_deleted_reservations_by_room` (IN rumid INT)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerno`, `room_name`, `userid`, `date`, `start_restime`, 
	`end_restime`, `verificationcode`, `attendance`)
	SELECT computer.`computerno`, room.`name`,`userid`, `date`, `start_restime`, 
		   `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations` reservation, 
		 `reservation_system`.`rooms` room, `reservation_system`.`computers` computer
	WHERE rumid = room.roomid AND  
	      reservation.computerid = computer.computerid AND room.roomid = computer.roomid;
    DELETE FROM `reservation_system`.`reservations`
    WHERE computerid IN (SELECT c.computerid FROM rooms r,computers c WHERE rumid = r.roomid
		  AND c.roomid = r.roomid);
END$$

DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `archive_deleted_reservations_by_computer` (IN compid INT)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerno`, `room_name`, `userid`, `date`, `start_restime`, 
	`end_restime`, `verificationcode`, `attendance`)
	SELECT computer.`computerno`, room.`name`,`userid`, `date`, `start_restime`, 
		   `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations` reservation, 
		 `reservation_system`.`rooms` room, `reservation_system`.`computers` computer
	WHERE compid = reservation.computerid AND 
		  reservation.computerid = computer.computerid AND room.roomid = computer.roomid;
    DELETE FROM `reservation_system`.`reservations` 
    WHERE compid = computerid;
END$$

DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `archive_unconfirmed_reservations` (IN confirmation_limit TIME)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerno`, `room_name`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`)
	SELECT computer.`computerno`, room.`name`,`userid`, `date`, `start_restime`, 
		   `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations` reservation, 
		 `reservation_system`.`rooms` room, `reservation_system`.`computers` computer
	WHERE TIMEDIFF(NOW(), reservation.`time_reserved`) >= confirmation_limit
		  AND reservation.computerid = computer.computerid AND room.roomid = computer.roomid;
    DELETE FROM `reservation_system`.`reservations`
    WHERE TIMEDIFF(NOW(), `time_reserved`) >= confirmation_limit;
END$$

DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `delete_closed_pcs` (IN rumid INT)
BEGIN
	DELETE FROM `reservation_system`.`closed_pcs`
	WHERE `computerid` IN (SELECT `computerid` FROM `reservations_system`.`computers`
						  WHERE `roomid` = rumid);
END$$

DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `archive_reservation` (IN reserveid INT)
BEGIN
	INSERT `reservation_system`.`archive_reservations`
	(`computerno`, `room_name`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`)
	SELECT computer.`computerno`, room.`name`,`userid`, `date`, `start_restime`, 
		   `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations` reservation, 
		 `reservation_system`.`rooms` room, `reservation_system`.`computers` computer
	WHERE reservation.reservationid = reserveid
		  AND reservation.computerid = computer.computerid AND room.roomid = computer.roomid;
    DELETE FROM `reservation_system`.`reservations`
    WHERE reservationid = reserveid;
END$$

DELIMITER ;