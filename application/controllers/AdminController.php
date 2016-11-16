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
        /*if(isset($_SESSION['email'])) {
            $this->initAdmin();
        }
        else {
          $this->signInView("");
        }*/
        $this->loadView("");
    }

    private function initAdmin(){

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/home'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer

    }

    public function loadView($viewName) {
        if(!isset($_SESSION['email'])) {
            $this->signInView("");
        }
        else {
            switch ($viewName) {
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
                default:
                    $this->initAdmin();
            }
        }
    }

    public function addView(){

        $data['buildings'] = $this->admin->queryAllBuildings();

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

    public function accView(){
        $data['administrators'] = $this->admin->queryAllAdministators();


        if($_SESSION['admin_typeid'] == 1)
            $data['moderators'] = $this->admin->queryAllModerators();
        else
            $data['moderators'] = $this->admin->queryModeratorsWithDepartmentID($_SESSION['admin_departmentid']);

            $data['departments'] = $this->admin->queryAllDepartments();


        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_accountManagement', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }


    public function schedulingView(){
        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $minuteInterval = $this->admin->getMinuteInterval();
        $maxNumberOfSlots = $this->admin->getMaxNumberOfSlots();
        $currentHour = date("H");
        $currentMinute = date("i");

        $data['buildings'] = $this->admin->queryAllBuildings();
        $data['colleges'] = $this->admin->queryColleges();
        $data['types'] = $this->admin->queryTypes();
        $data['times_today'] = $this->admin->getTimes($currentHour, $currentMinute, $minuteInterval, $this->student->getMinimumHour(), $this->student->getMaximumHour(), false);
        $data['times_tomorrow'] = $this->admin->getTimes(null, $currentMinute, $minuteInterval, $this->student->getMinimumHour(), $this->student->getMaximumHour(), true);

        $data['maxNumberOfSlots'] = $maxNumberOfSlots;

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

        $numAdded = $this->admin->insertRoomsAndComputers($roomData);

        $result = array(
            'result' => "success",
            'numAdded' => $numAdded,
        );

        echo json_encode($result);
    }

    public function updateRooms()
    {
        $changedData = $this->input->get("changedData");

        foreach ($changedData as $data) {
            // Query room data
            $room = $this->admin->queryRoomAtID($data[0]);


            // Check if deleted
            if ($data[2] == -1) {
                $this->admin->deleteRoom($data[0]);

                $result = array(
                    'result' => "success",
                );

                continue;
            }

            // Check if room name was changed
            if ($data[1] != $room['name']) {
                if ($this->admin->isExistingRoom($data[1])) {
                    // If room name already exists, cannot change
                    $result = array(
                        'result' => "name_invalid",
                    );
                } else {
                    // Update room name
                    $updateData = array(
                        'roomid' => $data[0],
                        'name' => $data[1],
                    );
                    $this->admin->updateRoomName($updateData);
                    $result = array(
                        'result' => "success",
                    );
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

                $result = array(
                    'result' => "success",
                );
            } else if ($data[2] < $room['capacity']) {
                // Remove computers
                $updateData = array(
                    'roomid' => $data[0],
                    'count' => $room['capacity'] - $data[2],
                );

                $this->admin->removeComputersFromRoom($updateData);

                $result = array(
                    'result' => "success",
                );
            }
        }

        echo json_encode($result);
    }

    public function addBuilding() {
        $buildingData = array(
            'name' => $this->input->get("buildingName")
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

        $this->admin->insertModerators($modData);

        echo json_encode("success");
    }

    public function updateModerators()
    {
        $changedData = $this->input->get("changedData");

        foreach ($changedData as $data) {
            // Query room data
            $mod = $this->admin->queryModeratorAtEmail($data[2]);


            // Check if deleted
            if ($data[4] == -1) {
                $this->admin->deleteRoom($data[0]);

                $result = array(
                    'result' => "success",
                );

                continue;
            }

            // Check if room name was changed
            if ($data[0] != $mod['name']) {
                if ($this->admin->isExistingRoom($data[1])) {
                    // If room name already exists, cannot change
                    $result = array(
                        'result' => "name_invalid",
                    );
                } else {
                    // Update room name
                    $updateData = array(
                        'roomid' => $data[0],
                        'name' => $data[1],
                    );
                    $this->admin->updateRoomName($updateData);
                    $result = array(
                        'result' => "success",
                    );
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

                $result = array(
                    'result' => "success",
                );
            } else if ($data[2] < $room['capacity']) {
                // Remove computers
                $updateData = array(
                    'roomid' => $data[0],
                    'count' => $room['capacity'] - $data[2],
                );

                $this->admin->removeComputersFromRoom($updateData);

                $result = array(
                    'result' => "success",
                );
            }
        }

        echo json_encode($result);
    }



    public function updateBusinessRules() {
        $id = $this->input->get("business_rulesid");

        $updateData = array(
            'interval' => $this->input->get("interval"),
            'limit' => $this->input->get("limit"),
            'accessibility' => $this->input->get("accessibility"),
            'reservation_expiry' => $this->input->get("reservation_expiry"),
            'confirmation_expiry' => $this->input->get("confirmation_expiry"),
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

        $this->admin->insertAdmins($adminData);

        echo json_encode("success");
    }

    public function getModDeptIDFromEmail(){
        $email = $this->input->get("email");

        $result = $this->admin->queryModDeptIDAtEmail($email);

        echo intval($result);
    }
}

