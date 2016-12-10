<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/* FOR PC STATUS */
define("PC_ENABLED", 1);
define("PC_DISABLED", 2);
define("PC_TAKEN", 3);

define("TABLE_AREA_TYPES", "area_types");
define("COLUMN_AREA_TYPEID", "area_typeid");
define("COLUMN_TYPE", "type");

define("TABLE_BUILDINGS", "buildings");
define("COLUMN_BUILDINGID", "buildingid");

define("TABLE_DEPARTMENTS", "departments");
define("COLUMN_DEPARTMENTID", "departmentid");

define("TABLE_ROOMS", "rooms");
define("COLUMN_ROOMID", "roomid");
define("COLUMN_NAME", "name");

define("TABLE_COMPUTERS", "computers");
define("COLUMN_COMPUTERID", "computerid");
define("COLUMN_COMPUTERNO", "computerno");

define("TABLE_COLLEGES", "colleges");
define("COLUMN_COLLEGEID", "collegeid");

define("TABLE_TYPES", "types");
define("COLUMN_TYPEID", "typeid");

define("TABLE_USERS", "users");
define("COLUMN_USERID", "userid");
define("COLUMN_LAST_NAME", "last_name");
define("COLUMN_FIRST_NAME", "first_name");
define("COLUMN_MIDDLE_NAME", "middle_name");
define("COLUMN_BIRTHDATE", "birthdate");

define("TABLE_RESERVATIONS", "reservations");
define("COLUMN_RESERVATIONID", "reservationid");
define("COLUMN_EMAIL", "email");
define("COLUMN_DATE", "date");
define("COLUMN_STARTRESTIME", "startrestime");
define("COLUMN_ENDRESTIME", "endrestime");
define("COLUMN_VERIFIED", "verified");
define("COLUMN_VERFICATIONCODE", "verificationcode");
define("COLUMN_ATTENDANCE", "attendance");
define("COLUMN_TIME_RESERVED", "time_reserved");

define("TABLE_EMAIL_EXTENSION", "email_extension");
define("COLUMN_EMAIL_EXTENSIONID", "email_extensionid");
define("COLUMN_EMAIL_EXTENSION", "email_extension");

define('TABLE_ADMINISTRATORS', "administrators");
define("COLUMN_ADMINISTRATORID", "administratorid");
define("COLUMN_ADMIN_DEPARTMENTID", "admin_departmentid");
define("COLUMN_PASSWORD", "password");

define("TABLE_ADMIN_TYPES", "admin_types");
define("COLUMN_ADMIN_TYPEID", "admin_typeid");
define("COLUMN_ADMIN_TYPE", "admin_type");

define("TABLE_MODERATORS", "moderators");
define("COLUMN_MODERATORID", "moderatorid");
define("COLUMN_MOD_DEPARTMENTID", "mod_departmentid");

define("TABLE_BUSINESS_RULES", "business_rules");
define("COLUMN_BUSINESS_RULESID", "business_rulesid");
define("COLUMN_INTERVAL", "interval");
define("COLUMN_LIMIT", "limit");
define("COLUMN_ACCESSIBILITY", "accessibility");
define("COLUMN_RESERVATION_EXPIRY", "reservation_expiry");
define("COLUMN_CONFIRMATION_EXPIRY", "confirmation_expiry");
define("COLUMN_START_TIME", "start_time");
define("COLUMN_END_TIME", "end_time");


define("TABLE_ARCHIVE_RESERVATIONS", "archive_reservations");

define("TABLE_TAG_MOD_ROOMS", "tag_mod_rooms");
define("COLUMN_TAG_MOD_ROOMSID", "tag_mod_roomsid");


/* ADMIN PAGES */
define("ADMIN_SCHEDULING", "scheduling");
define("ADMIN_AREA_MANAGEMENT", "area_management");
define("ADMIN_MODERATOR_MANAGEMENT", "mod_management");
define("ADMIN_ADMINISTRATOR_MANAGEMENT", "admin_management");
define("ADMIN_BUSINESS_RULES", "business_rules");
define("ADMIN_ACCOUNT_MANAGEMENT", "acc_management");

define("ADMIN_REPORTS", "reports");
/* ADMIN ACTIONS */
define("ADMIN_SIGN_IN", "sign_in");
define("ADMIN_SIGN_OUT", "sign_out");
define("ADMIN_ADD_ROOM", "add_room");
define("ADMIN_ADD_MODERATORS", "add_moderators");
define("ADMIN_UPDATE_ROOMS", "update_rooms");
define("ADMIN_UPDATE_BUSINESS_RULES", "update_business_rules");
define("ADMIN_ADD_ADMINS", "add_admins");
define("ADMIN_GET_MOD_DEPT_ID_FROM_EMAIL", "get_mod_dept_id_from_email");
define("ADMIN_UPDATE_MODERATORS", "update_moderators");
define("ADMIN_UPDATE_ADMINS", "update_admins");
define("ADMIN_GET_BUSINESS_RULES", "get_business_rules");
define("ADMIN_GET_ROOMS", "get_rooms");

/* SUPER USER PAGES */
define("SU_DEPARTMENTS", "departments");

/* SUPER USER ACTIONS */
define("SU_ADD_BUILDINGS", "add_buildings");
define("SU_ADD_DEPARTMENT", "add_department");

/* MODERATOR ACTIONS */
define("MODERATOR_SIGN_IN", "sign_in");
define("MODERATOR_GET_COMPUTERS", "get_computers");
define("MODERATOR_SIGN_OUT", "sign_out");
define("MODERATOR_BUSINESS_RULES", "getBusinessRules");
define("MODERATOR_DECODE_SLOTS", "decodeSlots");
define("MODERATOR_SET_RESERVATIONS_PRESENT", "setReservationsPresent");
define("MODERATOR_VERIFY_RESERVATION", "verifyReservation");
define("MODERATOR_GET_TIMES", "getTimes");

