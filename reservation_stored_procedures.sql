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

DELIMITER ;