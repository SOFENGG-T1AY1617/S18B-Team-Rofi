USE `reservation_system`;
DROP procedure IF EXISTS `archive_past_reservations`;

DELIMITER $$
USE `reservation_system`$$
-- date format => "YYYY-MM-DD" with quotations
-- time format => "HH:MM:SS" with quotations
CREATE PROCEDURE `archive_past_reservations` (IN curdate DATE, IN curtime TIME)
BEGIN
	INSERT INTO `reservation_system`.`archive_reservations`
	(`computerid`, `useridno`, `email`, `date`, `start_restime`, `end_restime`,
		`collegeid`, `typeid`, `verificationcode`, `attendance`)
	SELECT `computerid`, `useridno`, `email`, `date`, `start_restime`, `end_restime`, 
			`collegeid`, `typeid`, `verificationcode`, `attendance`
	FROM `reservation_system`.`reservations`
	WHERE curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`);
    DELETE FROM `reservation_system`.`reservations`
    WHERE curdate > `date` OR (curdate = `date` AND curtime >= `end_restime`);
END$$

DELIMITER ;