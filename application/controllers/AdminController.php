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

        $data['rooms'] = $this->admin->queryAllRooms();



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

    /*public function modView(){
        $data['moderators'] = $this->admin->queryAllModerators();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_moderator', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }
    public function adminView(){
        $data['administrators'] = $this->admin->queryAllAdministators();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_admin', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }*/

    public function accView(){
        $data['administrators'] = $this->admin->queryAllAdministators();
        $data['moderators'] = $this->admin->queryAllModerators();

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
        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_rules'); // $this->load->view('admin', $data); set to this if data is set
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

        $this->admin->insertRoomsAndComputers($roomData);

        echo json_encode("success");
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

}

