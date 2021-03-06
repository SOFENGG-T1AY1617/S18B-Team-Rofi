<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Controller';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* USER ROUTES  */
$route['getRooms'] = 'Controller/getRooms';
$route['getComputers'] = 'Controller/getComputers';
$route['submitReservation'] = 'Controller/submitReservation';
$route['verify/(:any)'] = 'Controller/verify/$1';
$route['getMyReservations'] = 'Controller/getMyReservations';
$route['checkType'] = 'Controller/checkType';
$route['getBusinessRules'] = 'Controller/getBusinessRules';
$route['getTimes'] = 'Controller/getTimes';

/* ADMIN ROUTE */
$route['admin'] = 'AdminController';

/*$route['admin/signIn'] = 'AdminController/signIn';
$route['admin/signOut'] = 'AdminController/signOut';
$route['admin/addRoom'] = 'AdminController/addRoom';
$route['admin/addModerators'] = 'AdminController/addModerators';
$route['admin/updateRooms'] = 'AdminController/updateRooms';
$route['admin/addBuilding'] = 'AdminController/addBuilding';
$route['admin/updateBusinessRules'] = 'AdminController/updateBusinessRules';
$route['admin/addAdmins'] = 'AdminController/addAdmins';
$route['admin/getModDeptIDFromEmail'] = 'AdminController/getModDeptIDFromEmail';
$route['admin/updateModerators'] = 'AdminController/updateModerators';
$route['admin/updateAdmins'] = 'AdminController/updateAdmins';
$route['admin/getBusinessRules'] = 'AdminController/getBusinessRules';
$route['admin/getRooms'] = 'AdminController/getRoomsByDepartmentID';*/

$route['admin/(:any)'] = 'AdminController/loadAction/$1';

/* MODERATOR ROUTE */
$route['moderator'] = 'ModeratorController';
$route['moderator/getBusinessRules'] = 'ModeratorController/getBusinessRules';

$route['moderator/(:any)'] = 'ModeratorController/loadAction/$1';

/*
 * NOTE
 * You no longer need to add routes for new views.
 * Just create a new constant in the constants.php file and add
 * case in loadView() to redirect to appropriate method.
 */

/*$route['admin/scheduling'] = 'AdminController/schedulingView';
$route['admin/area_management'] = 'AdminController/addView';
$route['admin/mod_management'] = 'AdminController/modView';
$route['admin/admin_management'] = 'AdminController/adminView';
$route['admin/business_rules'] = 'AdminController/ruleView';*/


/* ADMIN ROUTE */
$route['analytics'] = 'AnalyticsController';
$route['analytics/getData'] = 'AnalyticsController/getData';

/*Superuser - sorry im adding new routes*/
//$route['superuser'] = 'SuperuserController';
//$route['superuser/bldg'] = 'SuperuserController';
//$route['superuser/dept'] = 'SuperuserController/loadDepartmentView';
$route['superuser'] = 'SuperuserController';
$route['superuser/(:any)'] = 'SuperuserController/loadAction/$1';

