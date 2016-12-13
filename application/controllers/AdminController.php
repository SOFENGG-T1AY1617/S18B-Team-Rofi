<?php
/**
 * Created by PhpStorm.
 * User: psion
 * Date: 10/26/2016
 * Time: 8:59 PM
 */

header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller
{

    /**
     * Index Page for this controller.
     * @see https://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index()
    {
        date_default_timezone_set('Asia/Hong_Kong');
        $this->loadAction("");
    }

    private function initAdmin() {
        redirect('admin/' . ADMIN_REPORTS);
    }

    public function loadAction($action) {
        $this->admin->archivePastReservations(date("Y-m-d"), date("H:i:s"));
        $this->admin->archiveUnconfirmedReservations();
        if(!isset($_SESSION['email']) && $action != ADMIN_SIGN_IN) {
            $this->signInView("");
        }
        else {
            switch ($action) {
                case ADMIN_SCHEDULING:
                    $this->schedulingView();
                    break;
                case ADMIN_AREA_MANAGEMENT:
                    $this->addView();
                    break;
                case ADMIN_ACCOUNT_MANAGEMENT:
                    $this->accView();
                    break;
                case ADMIN_BUSINESS_RULES:
                    $this->ruleView();
                    break;
                case ADMIN_REPORTS:
                    $this->reportsView();
                    break;
                // Actions
                case ADMIN_SIGN_IN:
                    $this->signIn();
                    break;
                case ADMIN_SIGN_OUT:
                    $this->signOut();
                    break;
                case ADMIN_ADD_ROOM:
                    $this->addRoom();
                    break;
                case ADMIN_ADD_MODERATORS:
                    $this->addModerators();
                    break;
                case ADMIN_UPDATE_ROOMS:
                    $this->updateRooms();
                    break;
                case ADMIN_UPDATE_BUSINESS_RULES:
                    $this->updateBusinessRules();
                    break;
                case ADMIN_ADD_ADMINS:
                    $this->addAdmins();
                    break;
                case ADMIN_GET_MOD_DEPT_ID_FROM_EMAIL:
                    $this->getModDeptIDFromEmail();
                    break;
                case ADMIN_UPDATE_MODERATORS:
                    $this->updateModerators();
                    break;
                case ADMIN_UPDATE_ADMINS:
                    $this->updateAdmins();
                    break;
                case ADMIN_GET_BUSINESS_RULES:
                    $this->getBusinessRules();
                    break;
                case ADMIN_GET_ROOMS:
                    $this->getRoomsByDepartmentID();
                    break;
                case ADMIN_GET_TIMES:
                    $this->getTimes();
                    break;
                case ADMIN_GET_COMPUTERS:
                    $this->getComputers();
                    break;
                case ADMIN_ENABLE_SLOTS:
                    $this->enableSlots();
                    break;
                case ADMIN_DISABLE_SLOTS:
                    $this->disableSlots();
                    break;

                // Super user pages
                case SU_DEPARTMENTS:
                    $this->loadDepartmentView();
                    break;

                // Super user actions
                case SU_ADD_BUILDINGS:
                    $this->addBuilding();
                    break;
                case SU_ADD_DEPARTMENT:
                    $this->addDepartment();
                    break;

                default:
                    $this->initAdmin();
            }
        }
    }

    public function addView(){

        $data['buildings'] = $this->admin->queryAllBuildings();
        $data['roomTypes'] = $this->admin->queryAllRoomTypes();

        if($_SESSION['admin_typeid'] == 1)
            $data['rooms'] = $this->admin->queryAllRooms();
        else
            $data['rooms'] = $this->admin->queryRoomsWithDepartmentID($_SESSION['admin_departmentid']);



        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_add', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function signInView($errorMessage){
        //$data['login'] = $this->admin->queryAllModerators();

        $data['errorMessage'] = $errorMessage;

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/signIn', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function reportsView(){

        $data['buildings'] = $this->student->queryNonEmptyBuildings();
        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_reports',$data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function accView(){
        $data['administrators'] = $this->admin->queryAllAdministators();

        $data['departments'] = $this->admin->queryAllDepartments();
        $data['rooms'] = $this->admin->queryRoomsWithDepartmentID($_SESSION['admin_departmentid']);
        $modRooms = $this->admin->queryAllTagModRoom();

        $data['freeRooms']= $this->admin->queryFreeRoomsWithDepartmentID($_SESSION['admin_departmentid']);

       // $data['takenRooms']= $this->admin->queryTakenRoomsWithDepartmentID($_SESSION['admin_departmentid']);

        if($_SESSION['admin_typeid'] == 1)
            $data['moderators'] = $this->admin->queryAllModerators();
        else {
            $data['moderators'] = $this->admin->queryModeratorsWithDepartmentID($_SESSION['admin_departmentid']);

            foreach($modRooms as $tag){
                foreach ($data['moderators'] as $mod)
                    if($tag->moderatorid == $mod->moderatorid){
                        foreach ($data['rooms'] as $room) {
                            if($room->roomid == $tag->roomid)
                            $mod->room = $room->name;
                        }
                    }
            }

        }





        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_accountManagement', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function loadDepartmentView(){

        /*$data['administrators'] = $this->admin->queryAllAdministators();

        $data['departments'] = $this->admin->queryAllDepartments();*/
        $data['departments'] = $this->admin->queryAllDepartmentsAndAdmins();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/su_dept', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer

    }

    public function getBusinessRules() { // roomid
        $data = null;

        $getData = array(
            'roomid' => $this->input->get('roomid'),
        );

        if ($getData['roomid'] == 0)
            $data = $this->admin->queryBusinessRulesByDepartmentID($_SESSION['admin_departmentid']);
        else
            $data = $this->admin->queryBusinessRulesByRoomID($getData['roomid']);

        $result = array(
            'interval' => intval($data[0]->interval),
            'start_time' => $data[0]->start_time,
            'end_time' => $data[0]->end_time,
            'departmentid' => $data[0]->departmentid,
            'slotlimit' => intval($data[0]->limit)
        );

        echo json_encode($result);
    }

    public function getTimes () {
        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $times = null;

        $getData = array(
            'interval' => $this->input->get('interval'),
            'starttime' => $this->input->get('start_time'),
            'endtime' => $this->input->get('end_time'),
            'date' => $this->input->get('date')
        );

        if (date('l', strtotime($getData['date'])) != 'Sunday')
            $times = $this->admin->getTimes($getData['date'], $getData['interval'], $getData['starttime'], $getData['endtime'], strcmp($getData['date'], date("Y-m-d")) == 0);
        else
            $times = null;

        $data['times'] = null;
        $data['times_DISPLAY'] = null;

        foreach ($times as $time)
            $data['times'][] = date("H:i:s", $time);

        foreach ($times as $time)
            $data['times_DISPLAY'][] = date("h:i A", $time);

        echo json_encode($data);
    }

    public function getRoomsByDepartmentID() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),
        );

        $data = $this->admin->queryRoomsWithDepartmentIDAndBuildingID($_SESSION['admin_departmentid'], $getData['buildingid']);

        echo json_encode($data);
    }

    public function getComputers()
    {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),

            'roomid' => $this->input->get('roomid'),
            'date' => $this->input->get('currdate'),
            'time' => $this->input->get('currtime')
        );

        //$date = date("Y-m-d", strtotime($getData['date']));
        $date = $getData['date'];

        if (date('Y-m-d', strtotime($getData['date'])) == date("Y-m-d"))
            $time = $getData['time'];
        else
            $time = "00:00:00";

        if ($getData['roomid'] == 0)
            $data = array(
                'computers' => $this->admin->queryAllComputersAtBuildingIDAndDepartmentID($getData['buildingid'], $_SESSION['admin_departmentid']),
                'reservations' => $this->admin->queryReservationsAtBuildingIDOnDate($getData['buildingid'], $date),
                'disabledslots' => $this->admin->queryDisabledSlotsAtBuildingIDOnDateTime($getData['buildingid'], $date, $time),
                'date' => $date,
            );
        else
            $data = array(
                'computers' => $this->admin->queryComputersAtBuildingIDAndRoomID($getData['buildingid'], $getData['roomid']),
                'reservations' => $this->admin->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
                'disabledslots' => $this->admin->queryDisabledSlotsAtRoomIDOnDateTime($getData['roomid'], $date, $time),
                'date' => $date,
            );
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function schedulingView(){

        $data['buildings'] = $this->admin->queryBuildingsByDepartmentID($_SESSION['admin_departmentid']);

        date_default_timezone_set('Asia/Hong_Kong');

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_scheduling', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function ruleView(){
        if ($_SESSION['admin_typeid'] == 1)
            $data['rules'] = $this->admin->queryAllBusinessRules();
        else
            $data['rules'] = $this->admin->queryBusinessRulesByDepartmentID($_SESSION['admin_departmentid']);

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_rules', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }



    public function signIn() {
        $e = $_POST["adminEmail"];
        $p = $_POST["adminPassword"];
        // = $this->input->post('#adminPassword');



        if ($this->admin->isValidUser($e,$p)) {
            $admin = $this->admin->queryAdminAccount($e);
            $this->session->set_userdata($admin);
            $this->index();
        }
        else {
            $errorMessage = "Invalid email or password.";
            $this->signInView($errorMessage);
        }
    }

    public function signOut() {
        $this->session->sess_destroy();
        $this->session->unset_userdata('email');
        $this->index();
    }

    public function addRoom() {

        $roomData = array(
            'rooms' => $this->input->get("rooms"),
            'buildingid' => $this->input->get("buildingid"),
            'departmentid' => $_SESSION['admin_departmentid'],
        );

        $updateResult = $this->admin->insertRoomsAndComputers($roomData);

        $result = array(
            'result' => "success",
            'numAdded' => $updateResult['numAdded'],
            'notAdded' => $updateResult['notAdded'],
        );

        echo json_encode($result);
    }

    public function updateRooms()
    {
        $changedData = $this->input->get("changedData");

        $notDeleted = [];
        $notChanged = [];
        foreach ($changedData as $data) {
            // Query room data
            $room = $this->admin->queryRoomAtID($data[0]);


            // Check if deleted
            if ($data[2] == -1) {


                // Check if has reservations
                if ($this->admin->hasOngoingReservations($data[0])) {
                    $notDeleted[] = $data[1];
                }
                else {
                    $this->admin->deleteRoom($data[0]);
                }

                continue;
            }

            // Check if room name was changed
            if ($data[1] != $room['name']) {
                if ($this->admin->isExistingRoom($data[1])) {
                    // If room name already exists, cannot change
                    $notChanged[] = $data[1];
                } else {
                    // Update room name
                    $updateData = array(
                        'roomid' => $data[0],
                        'name' => $data[1],
                    );
                    $this->admin->updateRoomName($updateData);

                }
            }

            // Check if room capacity was changed
            if ($data[2] > $room['capacity']) {
                // Add computers

                $updateData = array(
                    'roomid' => $data[0],
                    'count' => $data[2] - $room['capacity'],
                );

                $this->admin->addComputersToRoom($updateData);


            } else if ($data[2] < $room['capacity']) {
                // Remove computers
                $updateData = array(
                    'roomid' => $data[0],
                    'count' => $room['capacity'] - $data[2],
                );

                $this->admin->removeComputersFromRoom($updateData);


            }
        }

        $result = array(
            'result' => "success",
            'notChanged' => $notChanged,
            'notDeleted' => $notDeleted,
        );

        echo json_encode($result);
    }

    public function addBuilding() {
        $buildingData = array(
            'name' => $this->input->get("buildingName"),
            'area_typeid'=>$this->input->get("optionsRadios")
        );

        $out =
            $this->admin->insertBuilding($buildingData);

        if($out)
            echo json_encode("success");
        else
            echo json_encode("fail");
    }

    public function addModerators() {

        $modData = array(
            'moderators' => $this->input->get("moderators"),
            'departmentid' => $_SESSION['admin_departmentid']
        );

        $updateResult = $this->admin->insertModerators($modData);

        $result = array(
            'result' => "success",
            'numAdded' => $updateResult['numAdded'],
            'notAdded' => $updateResult['notAdded'],
        );

        echo json_encode($result);

    }

    public function updateModerators()
    {

        $result = null;
        $changedData = $this->input->get("changedData");

        foreach ($changedData as $data) {
            // Query room data
            $mod = $this->admin->queryModeratorAtID($data[6]);


            if ($data[2] == -1) {
                $this->admin->deleteModerator($mod['moderatorid']);

                $result = array(
                    'result' => "success"
                );

                //continue;
            }else {

                // Check if first name was changed
                if ($data[0] != $mod['first_name']) {
                    // Update firts name
                    $updateData = array(
                        'id' => $mod['moderatorid'],
                        'fName' => $data[0],
                    );
                    $this->admin->updateModFirstName($updateData);
                    $result = array(
                        'result' => "success",
                    );

                }

                if ($data[1] != $mod['last_name']) {
                    // Update last name
                    $updateData = array(
                        'id' => $mod['moderatorid'],
                        'lName' => $data[1],
                    );
                    $this->admin->updateModLastName($updateData);
                    $result = array(
                        'result' => "success",
                    );

                }

                if ($data[4] == 0) {
                    // Update departmentid
                    if ($this->admin->isExistingTagModRoomByModID($mod['moderatorid'])) {
                        $this->admin->deleteTagModRoomsByModID($mod['moderatorid']);
                        $result = array(
                            'result' => "success",
                        );
                    }

                } else if(!$this->admin->isExistingTagModRoomByModIDAndRoomID($mod['moderatorid'],$data[4])){
                    if ($this->admin->isExistingTagModRoomByModID($mod['moderatorid']) && !$this->admin->isExistingTagModRoomByRoomID($data[4])) {
                        $this->admin->deleteTagModRoomsByModID($mod['moderatorid']);
                        $this->admin->insertTagModRoomAtModIDAndRoomID($mod['moderatorid'], $data[4]);
                        $result = array(
                            'result' => "success",
                        );
                    } else if (!$this->admin->isExistingTagModRoomByRoomID($data[4])) {
                        $this->admin->insertTagModRoomAtModIDAndRoomID($mod['moderatorid'], $data[4]);
                        $result = array(
                            'result' => "success",
                        );
                    } else
                        $result = array(
                            'result' => "room_invalid",
                        );
                }


                if ($data[2] != $mod['email']) {
                    if ($this->admin->isExistingModerator($data[2])) {
                        // If room name already exists, cannot change
                        $result = array(
                            'result' => "name_invalid",
                        );
                    } else {
                        // Update room name
                        $updateData = array(
                            'id' => $mod['moderatorid'],
                            'email' => $data[2],
                        );
                        $this->admin->updateModEmail($updateData);
                        $result = array(
                            'result' => "success",
                        );
                    }
                }
            }


        }

        echo json_encode($result);
    }

    public function updateAdmins()
    {

        $changedData = $this->input->get("changedData");

        foreach ($changedData as $data) {
            // Query room data
            $admin= $this->admin->queryAdminAtID($data[5]);




            if ($data[4] == -1) {
                $this->admin->deleteAdmin($admin['email']);

                $result = array(
                    'result' => "success"
                );

                continue;
            }


//TODO FIX DELETE PLEASE

            // Check if first name was changed
            if ($data[0] != $admin['first_name']) {
                // Update firts name
                $updateData = array(
                    'id' => $data[5],
                    'fName' => $data[0],
                );
                $this->admin->updateAdminFirstName($updateData);
                $result = array(
                    'result' => "success",
                );

            }

            if ($data[1] != $admin['last_name']) {
                // Update last name
                $updateData = array(
                    'id' => $data[5],
                    'lName' => $data[1],
                );
                $this->admin->updateAdminLastName($updateData);
                $result = array(
                    'result' => "success",
                );

            }

            if ($data[3] != $admin['admin_departmentid']) {
                // Update departmentid
                $updateData = array(
                    'id' => $data[5],
                    'dept' => $data[3],
                );
                $this->admin->updateAdminDepartment($updateData);
                $result = array(
                    'result' => "success",
                );

            }

            if ($data[2] != $admin['email']) {
                if ($this->admin->isExistingAdmin($data[2])) {
                    // If room name already exists, cannot change
                    $result = array(
                        'result' => "name_invalid",
                    );
                } else {
                    // Update room name
                    $updateData = array(
                        'id' => $data[5],
                        'email' => $data[2],
                    );
                    $this->admin->updateAdminEmail($updateData);
                    $result = array(
                        'result' => "success",
                    );
                }
            }


        }

        echo json_encode($result);
    }



    public function updateBusinessRules() {
        $id = $this->input->get("business_rulesid");

        $updateData = array(
            'interval' => $this->input->get("interval"),
            'limit' => $this->input->get("limit"),
            //'accessibility' => $this->input->get("accessibility"),
            'reservation_expiry' => $this->input->get("reservation_expiry"),
            'confirmation_expiry' => $this->input->get("confirmation_expiry"),
            'start_time' => $this->input->get("start_time"),
            'end_time' => $this->input->get("end_time"),
        );

        $this->admin->updateBusinessRules($id, $updateData);

        $result = array(
            'result' => "success",
        );

        echo json_encode($result);
    }

    public function addAdmins() {

        $adminData = array(
            'admins' => $this->input->get("admins")
        );

        $updateResult = $this->admin->insertAdmins($adminData);

        $result = array(
            'result' => "success",
            'numAdded' => $updateResult['numAdded'],
            'notAdded' => $updateResult['notAdded'],
        );

        echo json_encode($result);
    }

    public function getModDeptIDFromEmail(){
        $email = $this->input->get("email");

        $result = $this->admin->queryModDeptIDAtEmail($email);

        echo intval($result);
    }

    public function addDepartment() {
        $departmentData = array(
            COLUMN_NAME => $this->input->get("departmentName"),
        );

        $adminData = array(
            COLUMN_ADMIN_DEPARTMENTID => '',
            COLUMN_FIRST_NAME => $this->input->get("admin_firstName"),
            COLUMN_LAST_NAME => $this->input->get("admin_lastName"),
            COLUMN_EMAIL => $this->input->get("admin_email"),
            COLUMN_ADMIN_TYPEID => 2,
            COLUMN_PASSWORD => $this->getRandomPassword(),
        );

        $businessRulesData = array(
            COLUMN_DEPARTMENTID => '',
            COLUMN_INTERVAL => 15,
            COLUMN_LIMIT => 4,
            COLUMN_ACCESSIBILITY => 1,
            COLUMN_RESERVATION_EXPIRY => 15,
            COLUMN_CONFIRMATION_EXPIRY => 60,
            COLUMN_START_TIME => '6:00:00',
            COLUMN_END_TIME => '20:00:00',
        );

        $errors = $this->validateDepartmentInput($departmentData[COLUMN_NAME], $adminData[COLUMN_EMAIL]);

        if (count($errors) > 0) {
            $result = array(
                'result' => 'fail',
                'errors' => $errors,
            );
        }
        else if ($this->sendAccountEmail($adminData[COLUMN_FIRST_NAME], $adminData[COLUMN_EMAIL], $adminData[COLUMN_PASSWORD])){

            $this->admin->insertDepartment($departmentData);

            $departmentID = $this->admin->queryLatestDepartmentID();

            $adminData[COLUMN_ADMIN_DEPARTMENTID] = $departmentID;
            $this->admin->insertAdmin($adminData);

            $businessRulesData[COLUMN_DEPARTMENTID] = $departmentID;
            $this->admin->insertBusinessRules($businessRulesData);

            $result = array(
                'result' => 'success',
            );
        }

        echo json_encode($result);
    }

    private function getRandomPassword() {
        $length = 6;

        $this->load->helper('string');

        $password = substr(random_string('sha1'), 0, $length);

        return $password;

    }

    private function validateDepartmentInput($departmentName, $email) {
        $errors = [];
        if ($this->admin->isExistingDepartment($departmentName)) {
            $errors[] = "department name";
        }

        if ($this->admin->isExistingAdmin($email)) {
            $errors[] = "admin email";
        }

        return $errors;
    }

    public function sendAccountEmail($name, $email, $password) {
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_user' => 'dlsu.pc.reservation@gmail.com', // Email
            'smtp_pass' => 'DLSU1234!',
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE,
        );

        $message = "Dear " . $name . ",<br/><br/>
            Your account has been created with the following password: <br/><strong>". $password .
            "</strong><br/><br/>Thanks,
            <br/>DLSU Admin";


        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $config['mailtype'] = 'html';
        $this->email->from('dlsu.pc.reservation@gmail.com', "DLSU PC Reservation");
        $this->email->to($email);
        $this->email->subject("Your Account Password");
        $this->email->message($message);
        //$this->email->send();
        if($this->email->send())
        {
            return true;
        }
        else
        {
            show_error($this->email->print_debugger());
            return false;
        }
    }

    public function disableSlots () {
        $slots = $this->input->get('slots');
        $date = $this->input->get('currentDate');
        $hour = $this->input->get('hour');
        $minute = $this->input->get('minute');

        $newIDs = [];
        $updatedIDs = [];

        $updated = 0;
        $duration = $hour . ":" . $minute . ":00";

        foreach ($slots as $slot) {
            $arr = explode('_', $slot);

            if (count($arr) < 5) {
                $newIDs[] = $this->admin->disableSlot($slot, $date, $duration);
                $updatedIDs[] = $slot;

                $updated++;
            }

        }

        $result = array(
            'result' => "success",
            'updated' => $updated,
            'newIDs' => $newIDs,
            'updatedIDs' => $updatedIDs
        );

        echo json_encode($result);
    }

    public function enableSlots () {

        $slots = $this->input->get('slots');
        $updatedIDs = [];

        $updated = 0;

        foreach ($slots as $slot) {
            $arr = explode('_', $slot);

            if (count($arr) > 4) {
                $this->admin->enableSlotWithDisabledSlotID(intval($arr[4]));
                $updated++;
                $updatedIDs[] = $slot;
            }

        }

        $result = array(
            'result' => "success",
            'updated' => $updated,
            'updatedIDs' => $updatedIDs
        );

        echo json_encode($result);
    }
}

