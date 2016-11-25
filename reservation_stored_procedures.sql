USE `reservation_system`;
DROP procedure IF EXISTS `archive_past_reservations`;
DROP procedure IF EXISTS `archive_unconfirmed_reservations`;

DELIMITER $$
USE `reservation_system`$$
-- date format => "YYYY-MM-DD" with quotations
-- time format => "HH:MM:SS" with quotations
CREATE PROCEDURE `archive_past_reservations` (IN curdate DATE, IN curtime TIME)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerid`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`)
	SELECT `computerid`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations`
	WHERE curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`);
    DELETE FROM `reservation_system`.`reservations`
    WHERE curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`);
END$$


DELIMITER $$
USE `reservation_system`$$
CREATE PROCEDURE `archive_unconfirmed_reservations` (IN confirmation_limit TIME)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerid`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`)
	SELECT `computerid`, `userid`, `date`, `start_restime`, `end_restime`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations`
	WHERE TIMEDIFF(NOW(), `reservations`.`time_reserved`) >= confirmation_limit;
    DELETE FROM `reservation_system`.`reservations`
    WHERE TIMEDIFF(NOW(), `reservations`.`time_reserved`) >= confirmation_limit;
END$$

DELIMITER ;